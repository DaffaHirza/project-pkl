<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RecapitulationItemKanban extends Model
{
    protected $table = 'recapitulation_items_kanban';

    protected $fillable = [
        'recapitulation_id',
        'asset_id',
        'stage_start',
        'stage_end',
        'work_status',
        'activities',
        'notes',
        'next_actions',
    ];

    protected $casts = [
        'stage_start' => 'integer',
        'stage_end' => 'integer',
    ];

    public const STATUS_NOT_STARTED = 'not_started';
    public const STATUS_IN_PROGRESS = 'in_progress';
    public const STATUS_COMPLETED = 'completed';
    public const STATUS_BLOCKED = 'blocked';
    public const STATUS_PENDING_REVIEW = 'pending_review';

    public const WORK_STATUSES = [
        self::STATUS_NOT_STARTED => 'Belum Dikerjakan',
        self::STATUS_IN_PROGRESS => 'Sedang Dikerjakan',
        self::STATUS_COMPLETED => 'Selesai',
        self::STATUS_BLOCKED => 'Terhambat',
        self::STATUS_PENDING_REVIEW => 'Menunggu Review',
    ];

    public const WORK_STATUS_COLORS = [
        self::STATUS_NOT_STARTED => 'gray',
        self::STATUS_IN_PROGRESS => 'blue',
        self::STATUS_COMPLETED => 'green',
        self::STATUS_BLOCKED => 'red',
        self::STATUS_PENDING_REVIEW => 'yellow',
    ];

    public function recapitulation(): BelongsTo
    {
        return $this->belongsTo(RecapitulationKanban::class, 'recapitulation_id');
    }

    public function asset(): BelongsTo
    {
        return $this->belongsTo(AssetKanban::class, 'asset_id');
    }

    public function getWorkStatusLabelAttribute(): string
    {
        return self::WORK_STATUSES[$this->work_status] ?? $this->work_status;
    }

    public function getWorkStatusColorAttribute(): string
    {
        return self::WORK_STATUS_COLORS[$this->work_status] ?? 'gray';
    }

    public function getStageStartLabelAttribute(): string
    {
        return AssetKanban::STAGES[$this->stage_start] ?? "Stage {$this->stage_start}";
    }

    public function getStageEndLabelAttribute(): string
    {
        return AssetKanban::STAGES[$this->stage_end] ?? "Stage {$this->stage_end}";
    }

    public function getStageProgressAttribute(): int
    {
        return max(0, $this->stage_end - $this->stage_start);
    }

    public function getHasProgressAttribute(): bool
    {
        return $this->stage_end > $this->stage_start;
    }

    public function getProgressPercentageAttribute(): float
    {
        $stage = max(0, min(13, (int) $this->stage_end));

        return round(($stage / 13) * 100, 1);
    }

    public function generateActivitiesFromNotes(): string
    {
        if (!$this->asset) {
            return 'Tidak ada asset terkait.';
        }

        $recapitulation = $this->recapitulation;

        $notes = $this->asset->notes()
            ->whereBetween('created_at', [
                $recapitulation->period_start->copy()->startOfDay(),
                $recapitulation->period_end->copy()->endOfDay(),
            ])
            ->whereIn('type', ['stage_change', 'note', 'rejection', 'blocked'])
            ->orderBy('created_at')
            ->get();

        if ($notes->isEmpty()) {
            return 'Tidak ada aktivitas tercatat dalam periode ini.';
        }

        return $notes->map(function ($note) {
            $date = $note->created_at->format('d/m');

            $icon = match ($note->type) {
                'stage_change' => '📍',
                'rejection' => '❌',
                'blocked' => '⚠️',
                default => '📝',
            };

            return "{$icon} [{$date}] {$note->content}";
        })->implode("\n");
    }

    public function determineWorkStatus(): string
    {
        // Cek catatan penolakan / hambatan dulu
        // Karena kalau ada rejection/blocked, status kerja harus dianggap terhambat.
        if ($this->asset) {
            $hasBlockingNote = $this->asset->notes()
                ->whereIn('type', ['rejection', 'blocked'])
                ->where('created_at', '>=', now()->subDays(14))
                ->exists();

            if ($hasBlockingNote) {
                return self::STATUS_BLOCKED;
            }
        }

        // Completed jika sudah sampai stage akhir
        if ($this->stage_end >= 13) {
            return self::STATUS_COMPLETED;
        }

        // Pending review jika berada di stage review
        if (in_array($this->stage_end, [6, 10], true)) {
            return self::STATUS_PENDING_REVIEW;
        }

        // In progress jika ada kenaikan stage
        if ($this->stage_end > $this->stage_start) {
            return self::STATUS_IN_PROGRESS;
        }

        return self::STATUS_NOT_STARTED;
    }
}

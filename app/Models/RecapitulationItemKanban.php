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

    // Work status constants
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

    // ==========================================
    // RELATIONSHIPS
    // ==========================================

    public function recapitulation(): BelongsTo
    {
        return $this->belongsTo(RecapitulationKanban::class, 'recapitulation_id');
    }

    public function asset(): BelongsTo
    {
        return $this->belongsTo(AssetKanban::class, 'asset_id');
    }

    // ==========================================
    // ACCESSORS
    // ==========================================

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
        return $this->stage_end - $this->stage_start;
    }

    public function getHasProgressAttribute(): bool
    {
        return $this->stage_end > $this->stage_start;
    }

    public function getProgressPercentageAttribute(): float
    {
        // Calculate progress as percentage of total 13 stages
        return round(($this->stage_end / 13) * 100, 1);
    }

    // ==========================================
    // SCOPES
    // ==========================================

    public function scopeByStatus($query, string $status)
    {
        return $query->where('work_status', $status);
    }

    public function scopeWithProgress($query)
    {
        return $query->whereColumn('stage_end', '>', 'stage_start');
    }

    public function scopeBlocked($query)
    {
        return $query->where('work_status', self::STATUS_BLOCKED);
    }

    public function scopeCompleted($query)
    {
        return $query->where('work_status', self::STATUS_COMPLETED);
    }

    // ==========================================
    // METHODS
    // ==========================================

    /**
     * Auto-generate activities from asset notes within period
     */
    public function generateActivitiesFromNotes(): string
    {
        $recapitulation = $this->recapitulation;
        
        $notes = $this->asset->notes()
            ->whereBetween('created_at', [
                $recapitulation->period_start->startOfDay(),
                $recapitulation->period_end->endOfDay()
            ])
            ->whereIn('type', ['stage_change', 'note', 'approval'])
            ->orderBy('created_at')
            ->get();

        if ($notes->isEmpty()) {
            return 'Tidak ada aktivitas tercatat dalam periode ini.';
        }

        $activities = $notes->map(function ($note) {
            $date = $note->created_at->format('d/m');
            $type = match($note->type) {
                'stage_change' => '📍',
                'approval' => '✅',
                'rejection' => '❌',
                default => '📝',
            };
            return "{$type} [{$date}] {$note->content}";
        });

        return $activities->implode("\n");
    }

    /**
     * Determine work status based on stage progress
     */
    public function determineWorkStatus(): string
    {
        if ($this->stage_end >= 13) {
            return self::STATUS_COMPLETED;
        }
        
        if ($this->stage_end > $this->stage_start) {
            return self::STATUS_IN_PROGRESS;
        }
        
        return self::STATUS_NOT_STARTED;
    }
}

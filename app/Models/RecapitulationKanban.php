<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class RecapitulationKanban extends Model
{
    protected $table = 'recapitulations_kanban';

    protected $fillable = [
        'title',
        'period_start',
        'period_end',
        'summary',
        'status',
        'created_by',
        'published_at',
    ];

    protected $casts = [
        'period_start' => 'date',
        'period_end' => 'date',
        'published_at' => 'datetime',
    ];

    public const STATUS_DRAFT = 'draft';
    public const STATUS_PUBLISHED = 'published';

    public const STATUSES = [
        self::STATUS_DRAFT => 'Draft',
        self::STATUS_PUBLISHED => 'Dipublikasikan',
    ];

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function items(): HasMany
    {
        return $this->hasMany(RecapitulationItemKanban::class, 'recapitulation_id');
    }

    public function getStatusLabelAttribute(): string
    {
        return self::STATUSES[$this->status] ?? $this->status;
    }

    public function getPeriodLabelAttribute(): string
    {
        return $this->period_start->translatedFormat('d M Y') . ' - ' . $this->period_end->translatedFormat('d M Y');
    }

    public function getDurationDaysAttribute(): int
    {
        return $this->period_start->diffInDays($this->period_end) + 1;
    }

    public function getProgressSummaryAttribute(): array
    {
        $items = $this->relationLoaded('items') ? $this->items : $this->items()->get();

        return [
            'total' => $items->count(),
            'not_started' => $items->where('work_status', RecapitulationItemKanban::STATUS_NOT_STARTED)->count(),
            'in_progress' => $items->where('work_status', RecapitulationItemKanban::STATUS_IN_PROGRESS)->count(),
            'completed' => $items->where('work_status', RecapitulationItemKanban::STATUS_COMPLETED)->count(),
            'blocked' => $items->where('work_status', RecapitulationItemKanban::STATUS_BLOCKED)->count(),
            'pending_review' => $items->where('work_status', RecapitulationItemKanban::STATUS_PENDING_REVIEW)->count(),
        ];
    }

    public function getCompletionRateAttribute(): float
    {
        $summary = $this->progress_summary;

        if ($summary['total'] === 0) {
            return 0;
        }

        return round(($summary['completed'] / $summary['total']) * 100, 1);
    }

    public function scopePublished($query)
    {
        return $query->where('status', self::STATUS_PUBLISHED);
    }

    public function scopeDraft($query)
    {
        return $query->where('status', self::STATUS_DRAFT);
    }

    public function publish(): bool
    {
        return $this->update([
            'status' => self::STATUS_PUBLISHED,
            'published_at' => now(),
        ]);
    }

    public function unpublish(): bool
    {
        return $this->update([
            'status' => self::STATUS_DRAFT,
            'published_at' => null,
        ]);
    }

    public function isPublished(): bool
    {
        return $this->status === self::STATUS_PUBLISHED;
    }

    public static function generateTitle(Carbon $start, Carbon $end): string
    {
        return 'Rekapitulasi Minggu ' . $start->weekOfMonth . ' ' . $start->translatedFormat('F Y');
    }

    public static function getSuggestedPeriod(): array
    {
        $lastRecap = self::latest('period_end')->first();

        $start = $lastRecap
            ? $lastRecap->period_end->copy()->addDay()
            : now()->startOfWeek();

        $end = $start->copy()->addDays(6);

        return [
            'start' => $start,
            'end' => $end->greaterThan(now()) ? now() : $end,
        ];
    }
}

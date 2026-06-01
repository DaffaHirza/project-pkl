<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Carbon\Carbon;

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

    // Status constants
    public const STATUS_DRAFT = 'draft';
    public const STATUS_PUBLISHED = 'published';

    public const STATUSES = [
        self::STATUS_DRAFT => 'Draft',
        self::STATUS_PUBLISHED => 'Dipublikasikan',
    ];

    // ==========================================
    // RELATIONSHIPS
    // ==========================================

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function items(): HasMany
    {
        return $this->hasMany(RecapitulationItemKanban::class, 'recapitulation_id');
    }

    // ==========================================
    // ACCESSORS
    // ==========================================

    public function getStatusLabelAttribute(): string
    {
        return self::STATUSES[$this->status] ?? $this->status;
    }

    public function getPeriodLabelAttribute(): string
    {
        $start = $this->period_start->translatedFormat('d M Y');
        $end = $this->period_end->translatedFormat('d M Y');
        return "{$start} - {$end}";
    }

    public function getDurationDaysAttribute(): int
    {
        return $this->period_start->diffInDays($this->period_end) + 1;
    }

    public function getProgressSummaryAttribute(): array
    {
        $items = $this->items;
        
        return [
            'total' => $items->count(),
            'not_started' => $items->where('work_status', 'not_started')->count(),
            'in_progress' => $items->where('work_status', 'in_progress')->count(),
            'completed' => $items->where('work_status', 'completed')->count(),
            'blocked' => $items->where('work_status', 'blocked')->count(),
            'pending_review' => $items->where('work_status', 'pending_review')->count(),
        ];
    }

    public function getCompletionRateAttribute(): float
    {
        $total = $this->items->count();
        if ($total === 0) return 0;
        
        $completed = $this->items->where('work_status', 'completed')->count();
        return round(($completed / $total) * 100, 1);
    }

    // ==========================================
    // SCOPES
    // ==========================================

    public function scopePublished($query)
    {
        return $query->where('status', self::STATUS_PUBLISHED);
    }

    public function scopeDraft($query)
    {
        return $query->where('status', self::STATUS_DRAFT);
    }

    public function scopeForPeriod($query, $date)
    {
        return $query->where('period_start', '<=', $date)
                     ->where('period_end', '>=', $date);
    }

    public function scopeRecent($query, $limit = 10)
    {
        return $query->latest('period_end')->limit($limit);
    }

    // ==========================================
    // METHODS
    // ==========================================

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

    /**
     * Generate title based on period dates
     */
    public static function generateTitle(Carbon $start, Carbon $end): string
    {
        $weekNum = $start->weekOfMonth;
        $month = $start->translatedFormat('F Y');
        return "Rekapitulasi Minggu {$weekNum} {$month}";
    }

    /**
     * Get suggested period for next recapitulation (weekly)
     */
    public static function getSuggestedPeriod(): array
    {
        $lastRecap = self::latest('period_end')->first();
        
        if ($lastRecap) {
            $start = $lastRecap->period_end->copy()->addDay();
        } else {
            // Start from beginning of current week
            $start = now()->startOfWeek();
        }
        
        $end = $start->copy()->addDays(6); // 7 days period
        
        return [
            'start' => $start,
            'end' => min($end, now()), // Don't go beyond today
        ];
    }
}

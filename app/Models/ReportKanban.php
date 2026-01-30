<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReportKanban extends Model
{
    use HasFactory;

    protected $table = 'reports_kanban';

    protected $fillable = [
        'project_asset_id',
        'project_id', // Legacy, will be removed
        'type',
        'file_path',
        'version',
        'is_approved',
    ];

    protected $casts = [
        'is_approved' => 'boolean',
    ];

    /**
     * Report type options
     */
    public const TYPES = [
        'working_paper' => 'Kertas Kerja',
        'draft_report' => 'Draft Laporan',
        'final_report' => 'Laporan Final',
    ];

    /**
     * Get the asset for this report (NEW - Primary Relationship)
     */
    public function asset()
    {
        return $this->belongsTo(ProjectAsset::class, 'project_asset_id');
    }

    /**
     * Get the project through asset
     */
    public function project()
    {
        return $this->hasOneThrough(
            ProjectKanban::class,
            ProjectAsset::class,
            'id',
            'id',
            'project_asset_id',
            'project_id'
        );
    }

    /**
     * Get type label
     */
    public function getTypeLabelAttribute(): string
    {
        return self::TYPES[$this->type] ?? $this->type;
    }

    /**
     * Scope for draft reports
     */
    public function scopeDraft($query)
    {
        return $query->where('type', 'draft_report');
    }

    /**
     * Scope for final reports
     */
    public function scopeFinal($query)
    {
        return $query->where('type', 'final_report');
    }

    /**
     * Scope for working papers
     */
    public function scopeWorkingPaper($query)
    {
        return $query->where('type', 'working_paper');
    }

    /**
     * Scope for approved reports
     */
    public function scopeApproved($query)
    {
        return $query->where('is_approved', true);
    }
}

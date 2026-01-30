<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WorkingPaperKanban extends Model
{
    use HasFactory;

    protected $table = 'working_papers_kanban';

    protected $fillable = [
        'project_asset_id',
        'project_id', // Legacy, will be removed
        'analyst_id',
        'methodology',
        'assessed_value',
        'status',
        'notes',
    ];

    protected $casts = [
        'assessed_value' => 'decimal:2',
    ];

    /**
     * Methodology options
     */
    public const METHODOLOGIES = [
        'market' => 'Pendekatan Pasar',
        'cost' => 'Pendekatan Biaya',
        'income' => 'Pendekatan Pendapatan',
    ];

    /**
     * Status options
     */
    public const STATUS = [
        'draft' => 'Draft',
        'completed' => 'Selesai',
    ];

    /**
     * Get the asset for this working paper (NEW - Primary Relationship)
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
     * Get the analyst (user) for this working paper
     */
    public function analyst()
    {
        return $this->belongsTo(User::class, 'analyst_id');
    }

    /**
     * Get methodology label
     */
    public function getMethodologyLabelAttribute(): string
    {
        return self::METHODOLOGIES[$this->methodology] ?? $this->methodology ?? '-';
    }

    /**
     * Get status label
     */
    public function getStatusLabelAttribute(): string
    {
        return self::STATUS[$this->status] ?? $this->status;
    }

    /**
     * Scope for completed working papers
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    /**
     * Scope for draft working papers
     */
    public function scopeDraft($query)
    {
        return $query->where('status', 'draft');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProjectAsset extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'project_assets_kanban';

    protected $fillable = [
        'project_id',
        'asset_code',
        'name',
        'description',
        'asset_type',
        'location_address',
        'location_coordinates',
        'current_stage',
        'priority_status',
        'position',
        'target_completion_date',
        'notes',
    ];

    protected $casts = [
        'target_completion_date' => 'date',
    ];

    /**
     * Boot the model
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($asset) {
            if (empty($asset->asset_code)) {
                $year = date('Y');
                $count = self::whereYear('created_at', $year)->count();
                $asset->asset_code = sprintf('AST-%s-%04d', $year, $count + 1);
            }
        });
    }

    /**
     * Available stages for asset kanban workflow (Teknis)
     * Dimulai dari 'pending' karena lead/proposal/contract di level Project
     */
    public const STAGES = [
        'pending' => 'Menunggu',
        'inspection' => 'Inspeksi',
        'analysis' => 'Analisis',
        'review' => 'Review Internal',
        'client_approval' => 'Approval Klien',
        'final_report' => 'Laporan Final',
        'done' => 'Selesai',
    ];

    /**
     * Asset type options
     */
    public const ASSET_TYPES = [
        'tanah' => 'Tanah',
        'bangunan' => 'Bangunan',
        'tanah_bangunan' => 'Tanah & Bangunan',
        'mesin' => 'Mesin & Peralatan',
        'kendaraan' => 'Kendaraan',
        'inventaris' => 'Inventaris Kantor',
        'aset_tak_berwujud' => 'Aset Tak Berwujud',
        'lainnya' => 'Lainnya',
    ];

    /**
     * Priority status options
     */
    public const PRIORITY_STATUS = [
        'normal' => 'Normal',
        'warning' => 'Warning',
        'critical' => 'Critical',
    ];

    /**
     * Get the parent project
     */
    public function project()
    {
        return $this->belongsTo(ProjectKanban::class, 'project_id');
    }

    /**
     * Get the client through project
     */
    public function client()
    {
        return $this->hasOneThrough(
            KanbanClient::class,
            ProjectKanban::class,
            'id', // Foreign key on projects_kanban
            'id', // Foreign key on kanban_clients
            'project_id', // Local key on project_assets
            'client_id' // Local key on projects_kanban
        );
    }

    /**
     * Get all inspections for this asset
     */
    public function inspections()
    {
        return $this->hasMany(InspectionKanban::class, 'project_asset_id');
    }

    /**
     * Get the latest inspection
     */
    public function latestInspection()
    {
        return $this->hasOne(InspectionKanban::class, 'project_asset_id')->latestOfMany();
    }

    /**
     * Get all working papers for this asset
     */
    public function workingPapers()
    {
        return $this->hasMany(WorkingPaperKanban::class, 'project_asset_id');
    }

    /**
     * Get the latest working paper
     */
    public function latestWorkingPaper()
    {
        return $this->hasOne(WorkingPaperKanban::class, 'project_asset_id')->latestOfMany();
    }

    /**
     * Get all reports for this asset
     */
    public function reports()
    {
        return $this->hasMany(ReportKanban::class, 'project_asset_id');
    }

    /**
     * Get the final report
     */
    public function finalReport()
    {
        return $this->hasOne(ReportKanban::class, 'project_asset_id')
            ->where('type', 'final_report')
            ->latestOfMany();
    }

    /**
     * Get all approvals for this asset
     */
    public function approvals()
    {
        return $this->hasMany(ApprovalKanban::class, 'project_asset_id');
    }

    /**
     * Get all documents for this asset
     */
    public function documents()
    {
        return $this->hasMany(DocumentKanban::class, 'project_asset_id');
    }

    /**
     * Get all activities for this asset
     */
    public function activities()
    {
        return $this->hasMany(ActivityKanban::class, 'project_asset_id');
    }

    /**
     * Get stage label
     */
    public function getStageLabelAttribute(): string
    {
        return self::STAGES[$this->current_stage] ?? $this->current_stage;
    }

    /**
     * Get asset type label
     */
    public function getAssetTypeLabelAttribute(): string
    {
        return self::ASSET_TYPES[$this->asset_type] ?? $this->asset_type;
    }

    /**
     * Get priority status label
     */
    public function getPriorityStatusLabelAttribute(): string
    {
        return self::PRIORITY_STATUS[$this->priority_status] ?? $this->priority_status;
    }

    /**
     * Move to next stage
     */
    public function moveToStage(string $stage): bool
    {
        if (array_key_exists($stage, self::STAGES)) {
            $this->current_stage = $stage;
            $saved = $this->save();
            
            // Update parent project progress
            if ($saved) {
                $this->project->updateProgress();
            }
            
            return $saved;
        }
        return false;
    }

    /**
     * Check if asset is completed
     */
    public function isCompleted(): bool
    {
        return $this->current_stage === 'done';
    }

    /**
     * Check if asset is overdue
     */
    public function isOverdue(): bool
    {
        return $this->due_date && $this->due_date->isPast() && !$this->isCompleted();
    }

    /**
     * Get progress percentage based on stage
     */
    public function getProgressPercentageAttribute(): int
    {
        $stageOrder = array_keys(self::STAGES);
        $currentIndex = array_search($this->current_stage, $stageOrder);
        $totalStages = count($stageOrder) - 1; // -1 because we start from 0
        
        return $totalStages > 0 ? round(($currentIndex / $totalStages) * 100) : 0;
    }

    /**
     * Generate asset code
     */
    public static function generateAssetCode(?int $projectId = null): string
    {
        $year = date('Y');
        $lastAsset = self::whereYear('created_at', $year)
            ->whereNotNull('asset_code')
            ->orderBy('id', 'desc')
            ->first();
        
        $sequence = 1;
        if ($lastAsset && $lastAsset->asset_code) {
            $lastPart = substr($lastAsset->asset_code, -4);
            $sequence = (int) $lastPart + 1;
        }
        
        return sprintf('AST-%s-%04d', $year, $sequence);
    }

    /**
     * Scope for assets by stage
     */
    public function scopeByStage($query, string $stage)
    {
        return $query->where('current_stage', $stage);
    }

    /**
     * Scope for assets by priority
     */
    public function scopeByPriority($query, string $priority)
    {
        return $query->where('priority_status', $priority);
    }

    /**
     * Scope for overdue assets
     */
    public function scopeOverdue($query)
    {
        return $query->whereNotNull('due_date')
            ->where('due_date', '<', now())
            ->where('current_stage', '!=', 'done');
    }

    /**
     * Scope for incomplete assets
     */
    public function scopeIncomplete($query)
    {
        return $query->where('current_stage', '!=', 'done');
    }

    /**
     * Scope for completed assets
     */
    public function scopeCompleted($query)
    {
        return $query->where('current_stage', 'done');
    }
}

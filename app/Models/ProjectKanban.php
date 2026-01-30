<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProjectKanban extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'projects_kanban';

    protected $fillable = [
        'client_id',
        'project_code',
        'name',
        'location',
        'current_stage',
        'priority_status',
        'due_date',
        'total_assets',
    ];

    protected $casts = [
        'due_date' => 'date',
    ];

    /**
     * Available stages for project workflow
     * Lead -> Proposal -> Contract -> Inspection -> Analysis -> Review -> Client Approval -> Final Report -> Invoicing -> Done
     */
    public const STAGES = [
        'lead' => 'Lead / Permintaan',
        'proposal' => 'Proposal',
        'contract' => 'Kontrak',
        'inspection' => 'Inspeksi',
        'analysis' => 'Analisis / Kertas Kerja',
        'review' => 'Review Internal',
        'client_approval' => 'Approval Klien',
        'final_report' => 'Laporan Final',
        'invoicing' => 'Penagihan',
        'done' => 'Selesai',
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
     * Get the client for this project
     */
    public function client()
    {
        return $this->belongsTo(KanbanClient::class, 'client_id');
    }

    /**
     * Get all assets/objects for this project
     */
    public function assets()
    {
        return $this->hasMany(ProjectAsset::class, 'project_id');
    }

    /**
     * Get assets grouped by stage (for kanban view)
     */
    public function getAssetsByStageAttribute()
    {
        $assets = $this->assets;
        $stages = ProjectAsset::STAGES;
        $result = [];
        
        foreach ($stages as $stageKey => $stageLabel) {
            $result[$stageKey] = $assets->where('current_stage', $stageKey)->values();
        }
        
        return $result;
    }

    /**
     * Get all proposals for this project (GLOBAL)
     */
    public function proposals()
    {
        return $this->hasMany(ProposalKanban::class, 'project_id');
    }

    /**
     * Get the latest proposal
     */
    public function latestProposal()
    {
        return $this->hasOne(ProposalKanban::class, 'project_id')->latestOfMany();
    }

    /**
     * Get all contracts for this project (GLOBAL)
     */
    public function contracts()
    {
        return $this->hasMany(ContractKanban::class, 'project_id');
    }

    /**
     * Get the latest contract
     */
    public function latestContract()
    {
        return $this->hasOne(ContractKanban::class, 'project_id')->latestOfMany();
    }

    /**
     * Get all invoices for this project (GLOBAL)
     */
    public function invoices()
    {
        return $this->hasMany(InvoiceKanban::class, 'project_id');
    }

    /**
     * Get all approvals for this project (Project Level)
     */
    public function approvals()
    {
        return $this->hasMany(ApprovalKanban::class, 'project_id')
            ->where('approval_level', 'project');
    }

    /**
     * Get all documents for this project (Project Level)
     */
    public function documents()
    {
        return $this->hasMany(DocumentKanban::class, 'project_id')
            ->whereNull('project_asset_id');
    }

    /**
     * Get all activities for this project (Project Level)
     */
    public function activities()
    {
        return $this->hasMany(ActivityKanban::class, 'project_id');
    }

    // ========================================
    // LEGACY RELATIONSHIPS (Via Assets)
    // These now aggregate from all assets
    // ========================================

    /**
     * Get all inspections across all assets
     */
    public function inspections()
    {
        return $this->hasManyThrough(
            InspectionKanban::class,
            ProjectAsset::class,
            'project_id', // Foreign key on project_assets
            'project_asset_id', // Foreign key on inspections_kanban
            'id', // Local key on projects_kanban
            'id' // Local key on project_assets
        );
    }

    /**
     * Get all working papers across all assets
     */
    public function workingPapers()
    {
        return $this->hasManyThrough(
            WorkingPaperKanban::class,
            ProjectAsset::class,
            'project_id',
            'project_asset_id',
            'id',
            'id'
        );
    }

    /**
     * Get all reports across all assets
     */
    public function reports()
    {
        return $this->hasManyThrough(
            ReportKanban::class,
            ProjectAsset::class,
            'project_id',
            'project_asset_id',
            'id',
            'id'
        );
    }

    // ========================================
    // HELPER METHODS
    // ========================================

    /**
     * Get stage label
     */
    public function getStageLabelAttribute(): string
    {
        return self::STAGES[$this->current_stage] ?? $this->current_stage;
    }

    /**
     * Move to stage
     */
    public function moveToStage(string $stage): bool
    {
        if (array_key_exists($stage, self::STAGES)) {
            $this->current_stage = $stage;
            return $this->save();
        }
        return false;
    }

    /**
     * Update progress based on assets completion
     */
    public function updateProgress(): void
    {
        $totalAssets = $this->assets()->count();
        $completedAssets = $this->assets()->completed()->count();
        
        $this->total_assets = $totalAssets;
        
        // Move to invoicing if all assets done
        if ($totalAssets > 0 && $completedAssets === $totalAssets) {
            if ($this->current_stage === 'contract') {
                $this->current_stage = 'invoicing';
            }
        }
        
        $this->save();
    }

    /**
     * Get overall progress percentage
     */
    public function getProgressPercentageAttribute(): int
    {
        $totalAssets = $this->assets()->count();
        if ($totalAssets === 0) {
            return 0;
        }
        
        $completedAssets = $this->assets()->completed()->count();
        return round(($completedAssets / $totalAssets) * 100);
    }

    /**
     * Get assets progress summary
     */
    public function getAssetsProgressAttribute(): array
    {
        $assets = $this->assets;
        $stages = ProjectAsset::STAGES;
        
        $summary = [];
        foreach ($stages as $key => $label) {
            $summary[$key] = [
                'label' => $label,
                'count' => $assets->where('current_stage', $key)->count(),
            ];
        }
        
        return $summary;
    }

    /**
     * Check if all assets are completed
     */
    public function allAssetsCompleted(): bool
    {
        $totalAssets = $this->assets()->count();
        $completedAssets = $this->assets()->completed()->count();
        
        return $totalAssets > 0 && $completedAssets === $totalAssets;
    }

    /**
     * Generate project code
     */
    public static function generateProjectCode(): string
    {
        $year = date('Y');
        $lastProject = self::whereYear('created_at', $year)
            ->orderBy('id', 'desc')
            ->first();
        
        $sequence = $lastProject ? (int) substr($lastProject->project_code, -4) + 1 : 1;
        
        return sprintf('PRJ-%s-%04d', $year, $sequence);
    }

    /**
     * Scope for projects by stage
     */
    public function scopeByStage($query, string $stage)
    {
        return $query->where('current_stage', $stage);
    }

    /**
     * Scope for projects not done
     */
    public function scopeOngoing($query)
    {
        return $query->where('current_stage', '!=', 'done');
    }
}

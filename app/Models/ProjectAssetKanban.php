<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\ProjectKanban;
use App\Models\AssetDocumentKanban;
use App\Models\AssetNoteKanban;

class ProjectAssetKanban extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'project_assets_kanban';

    protected $fillable = [
        'project_id',
        'asset_code',
        'name',
        'description',
        'asset_type',
        'location',
        'current_stage',
        'priority',
        'position',
    ];

    protected $casts = [
        'current_stage' => 'integer',
        'position' => 'integer',
    ];

    // ==========================================
    // CONSTANTS - 13 STAGES
    // ==========================================

    public const STAGES = [
        1 => 'Inisiasi',
        2 => 'Penawaran',
        3 => 'Kesepakatan',
        4 => 'Eksekusi Lapangan',
        5 => 'Analisis',
        6 => 'Review 1',
        7 => 'Draft Resume',
        8 => 'Approval Klien',
        9 => 'Draft Laporan',
        10 => 'Review 2',
        11 => 'Finalisasi',
        12 => 'Delivery & Payment',
        13 => 'Arsip',
    ];

    public const ASSET_TYPES = [
        'tanah' => 'Tanah',
        'bangunan' => 'Bangunan',
        'tanah_bangunan' => 'Tanah & Bangunan',
        'mesin' => 'Mesin & Peralatan',
        'kendaraan' => 'Kendaraan',
        'inventaris' => 'Inventaris',
        'aset_tak_berwujud' => 'Aset Tak Berwujud',
        'lainnya' => 'Lainnya',
    ];

    public const PRIORITIES = [
        'normal' => 'Normal',
        'warning' => 'Warning',
        'critical' => 'Critical',
    ];

    // ==========================================
    // BOOT
    // ==========================================

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($asset) {
            if (empty($asset->asset_code)) {
                $year = date('Y');
                $count = self::whereYear('created_at', $year)->withTrashed()->count();
                $asset->asset_code = sprintf('AST-%s-%04d', $year, $count + 1);
            }
        });
    }

    // ==========================================
    // RELATIONSHIPS
    // ==========================================

    public function project()
    {
        return $this->belongsTo(ProjectKanban::class, 'project_id');
    }

    public function documents()
    {
        return $this->hasMany(AssetDocumentKanban::class, 'asset_id');
    }

    public function notes()
    {
        return $this->hasMany(AssetNoteKanban::class, 'asset_id');
    }

    // ==========================================
    // ACCESSORS
    // ==========================================

    public function getStageLabelAttribute(): string
    {
        return self::STAGES[$this->current_stage] ?? 'Unknown';
    }

    public function getAssetTypeLabelAttribute(): string
    {
        return self::ASSET_TYPES[$this->asset_type] ?? $this->asset_type;
    }

    public function getPriorityLabelAttribute(): string
    {
        return self::PRIORITIES[$this->priority] ?? $this->priority;
    }

    public function getProgressAttribute(): int
    {
        return (int) round(($this->current_stage / 13) * 100);
    }

    // ==========================================
    // STAGE METHODS
    // ==========================================

    public function moveToStage(int $stage, ?int $userId = null, ?string $note = null): bool
    {
        if ($stage < 1 || $stage > 13) return false;

        $oldStage = $this->current_stage;
        $this->current_stage = $stage;
        $saved = $this->save();

        if ($saved && $userId) {
            $this->notes()->create([
                'user_id' => $userId,
                'stage' => $stage,
                'type' => 'stage_change',
                'content' => $note ?? "Pindah dari " . self::STAGES[$oldStage] . " ke " . self::STAGES[$stage],
            ]);
        }

        return $saved;
    }

    public function moveToNextStage(?int $userId = null, ?string $note = null): bool
    {
        if ($this->current_stage >= 13) return false;
        return $this->moveToStage($this->current_stage + 1, $userId, $note);
    }

    public function moveToPreviousStage(?int $userId = null, ?string $note = null): bool
    {
        if ($this->current_stage <= 1) return false;
        return $this->moveToStage($this->current_stage - 1, $userId, $note);
    }

    // ==========================================
    // HELPER METHODS
    // ==========================================

    public function isCompleted(): bool
    {
        return $this->current_stage === 13;
    }

    public function getDocumentsByStage(?int $stage = null)
    {
        $query = $this->documents();
        if ($stage) {
            $query->where('stage', $stage);
        }
        return $query->latest()->get();
    }

    public function getNotesByStage(?int $stage = null)
    {
        $query = $this->notes();
        if ($stage) {
            $query->where('stage', $stage);
        }
        return $query->latest()->get();
    }

    // ==========================================
    // SCOPES
    // ==========================================

    public function scopeAtStage($query, int $stage)
    {
        return $query->where('current_stage', $stage);
    }

    public function scopeCompleted($query)
    {
        return $query->where('current_stage', 13);
    }

    public function scopeActive($query)
    {
        return $query->where('current_stage', '<', 13);
    }

    public function scopePriority($query, string $priority)
    {
        return $query->where('priority', $priority);
    }

    public function scopeNeedsAttention($query)
    {
        return $query->whereIn('priority', ['warning', 'critical'])
            ->where('current_stage', '<', 13);
    }

    public function scopeForKanban($query)
    {
        return $query->select('id', 'project_id', 'name', 'asset_code', 'current_stage', 'priority', 'position');
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('position');
    }
}

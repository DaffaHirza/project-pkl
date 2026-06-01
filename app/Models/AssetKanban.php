<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\ClientKanban;
use App\Models\AssetDocumentKanban;
use App\Models\AssetNoteKanban;

class AssetKanban extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'assets_kanban';

    protected $fillable = [
        'client_id',
        'name',
        'asset_type',
        'location',
        'current_stage',
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


    // ==========================================
    // RELATIONSHIPS
    // ==========================================

    /**
     * Client pemilik asset (debitur, pt_cv, atau pt_anak)
     */
    public function client()
    {
        return $this->belongsTo(ClientKanban::class, 'client_id');
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

    public function getProgressAttribute(): int
    {
        return (int) round(($this->current_stage / 13) * 100);
    }

    /**
     * Mendapatkan bank jika asset ini milik debitur
     */
    public function getBankAttribute()
    {
        if ($this->client?->type === 'debitur') {
            return $this->client->parent;
        }
        return null;
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

    public function scopeOrdered($query)
    {
        return $query->orderBy('position');
    }
}

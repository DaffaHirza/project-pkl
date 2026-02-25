<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\ProjectAssetKanban;
use App\Models\User;

class AssetNoteKanban extends Model
{
    use HasFactory;

    protected $table = 'asset_notes_kanban';

    protected $fillable = [
        'asset_id',
        'user_id',
        'stage',
        'type',
        'content',
    ];

    protected $casts = [
        'stage' => 'integer',
    ];

    // ==========================================
    // CONSTANTS
    // ==========================================

    public const TYPES = [
        'note' => 'Catatan',
        'stage_change' => 'Perubahan Stage',
        'approval' => 'Approval',
        'rejection' => 'Penolakan',
    ];

    // ==========================================
    // RELATIONSHIPS
    // ==========================================

    public function asset()
    {
        return $this->belongsTo(ProjectAssetKanban::class, 'asset_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // ==========================================
    // ACCESSORS
    // ==========================================

    public function getStageLabelAttribute(): string
    {
        return ProjectAssetKanban::STAGES[$this->stage] ?? 'Unknown';
    }

    public function getTypeLabelAttribute(): string
    {
        return self::TYPES[$this->type] ?? $this->type;
    }

    // ==========================================
    // METHODS
    // ==========================================

    public function isStageChange(): bool
    {
        return $this->type === 'stage_change';
    }

    public function isApproval(): bool
    {
        return $this->type === 'approval';
    }

    public function isRejection(): bool
    {
        return $this->type === 'rejection';
    }

    public function isNote(): bool
    {
        return $this->type === 'note';
    }

    // ==========================================
    // SCOPES
    // ==========================================

    public function scopeAtStage($query, int $stage)
    {
        return $query->where('stage', $stage);
    }

    public function scopeOfType($query, string $type)
    {
        return $query->where('type', $type);
    }

    public function scopeNotesOnly($query)
    {
        return $query->where('type', 'note');
    }

    public function scopeActivityLog($query)
    {
        return $query->whereIn('type', ['stage_change', 'approval', 'rejection']);
    }
}

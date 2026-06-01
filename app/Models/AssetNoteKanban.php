<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\AssetKanban;
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
        return $this->belongsTo(AssetKanban::class, 'asset_id');
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
        return AssetKanban::STAGES[$this->stage] ?? 'Unknown';
    }

    public function getTypeLabelAttribute(): string
    {
        return self::TYPES[$this->type] ?? $this->type;
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

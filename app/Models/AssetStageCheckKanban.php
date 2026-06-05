<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AssetStageCheckKanban extends Model
{
    protected $table = 'asset_stage_checks_kanban';

    protected $fillable = [
        'asset_id',
        'stage',
        'is_checked',
        'checked_by',
        'checked_at',
        'note',
    ];

    protected $casts = [
        'stage' => 'integer',
        'is_checked' => 'boolean',
        'checked_at' => 'datetime',
    ];

    public function asset()
    {
        return $this->belongsTo(AssetKanban::class, 'asset_id');
    }

    public function checker()
    {
        return $this->belongsTo(User::class, 'checked_by');
    }

    public function getStageLabelAttribute(): string
    {
        return AssetKanban::STAGES[$this->stage] ?? "Stage {$this->stage}";
    }
}
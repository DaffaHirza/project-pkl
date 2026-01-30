<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ApprovalKanban extends Model
{
    use HasFactory;

    protected $table = 'approvals_kanban';

    protected $fillable = [
        'project_id',
        'project_asset_id',
        'user_id',
        'stage',
        'approval_level',
        'status',
        'comments',
    ];

    /**
     * Approval stage options
     */
    public const STAGES = [
        'internal_review' => 'Review Internal',
        'client_approval' => 'Approval Klien',
    ];

    /**
     * Approval status options
     */
    public const STATUS = [
        'approved' => 'Disetujui',
        'rejected' => 'Ditolak / Revisi',
    ];

    /**
     * Approval level options
     */
    public const LEVELS = [
        'asset' => 'Per Objek',
        'project' => 'Proyek',
    ];

    /**
     * Get the project for this approval
     */
    public function project()
    {
        return $this->belongsTo(ProjectKanban::class, 'project_id');
    }

    /**
     * Get the asset for this approval (if asset level)
     */
    public function asset()
    {
        return $this->belongsTo(ProjectAsset::class, 'project_asset_id');
    }

    /**
     * Get the user for this approval (null if client)
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Check if approval is from internal
     */
    public function isInternal(): bool
    {
        return $this->stage === 'internal_review';
    }

    /**
     * Check if approval is from client
     */
    public function isClientApproval(): bool
    {
        return $this->stage === 'client_approval';
    }

    /**
     * Scope for approved
     */
    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    /**
     * Scope for rejected
     */
    public function scopeRejected($query)
    {
        return $query->where('status', 'rejected');
    }

    /**
     * Scope for asset level approvals
     */
    public function scopeAssetLevel($query)
    {
        return $query->where('approval_level', 'asset');
    }

    /**
     * Scope for project level approvals
     */
    public function scopeProjectLevel($query)
    {
        return $query->where('approval_level', 'project');
    }

    /**
     * Check if this is an asset level approval
     */
    public function isAssetLevel(): bool
    {
        return $this->approval_level === 'asset';
    }

    /**
     * Check if this is a project level approval
     */
    public function isProjectLevel(): bool
    {
        return $this->approval_level === 'project';
    }
}

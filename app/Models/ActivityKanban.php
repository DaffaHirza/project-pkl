<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ActivityKanban extends Model
{
    use HasFactory;

    protected $table = 'activities_kanban';

    protected $fillable = [
        'project_id',
        'project_asset_id',
        'user_id',
        'activity_type',
        'stage_context',
        'description',
    ];

    /**
     * Activity type options
     */
    public const TYPES = [
        'stage_move' => 'Pindah Tahap',
        'comment' => 'Komentar',
        'approval' => 'Approval',
        'rejection' => 'Penolakan',
        'obstacle' => 'Laporan Halangan',
        'upload' => 'Upload File',
        'asset_created' => 'Objek Ditambahkan',
        'asset_completed' => 'Objek Selesai',
    ];

    /**
     * Get the project for this activity
     */
    public function project()
    {
        return $this->belongsTo(ProjectKanban::class, 'project_id');
    }

    /**
     * Get the asset for this activity (if asset specific)
     */
    public function asset()
    {
        return $this->belongsTo(ProjectAsset::class, 'project_asset_id');
    }

    /**
     * Get the user for this activity
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Get activity type label
     */
    public function getTypeLabelAttribute(): string
    {
        return self::TYPES[$this->activity_type] ?? $this->activity_type;
    }

    /**
     * Create stage move activity for PROJECT
     */
    public static function logStageMove(ProjectKanban $project, User $user, string $fromStage, string $toStage): self
    {
        return self::create([
            'project_id' => $project->id,
            'user_id' => $user->id,
            'activity_type' => 'stage_move',
            'stage_context' => $toStage,
            'description' => "Memindahkan project dari '{$fromStage}' ke '{$toStage}'",
        ]);
    }

    /**
     * Create stage move activity for ASSET
     */
    public static function logAssetStageMove(ProjectAsset $asset, User $user, string $fromStage, string $toStage): self
    {
        return self::create([
            'project_id' => $asset->project_id,
            'project_asset_id' => $asset->id,
            'user_id' => $user->id,
            'activity_type' => 'stage_move',
            'stage_context' => $toStage,
            'description' => "Memindahkan objek '{$asset->name}' dari '{$fromStage}' ke '{$toStage}'",
        ]);
    }

    /**
     * Create comment activity for project
     */
    public static function logComment(ProjectKanban $project, User $user, string $comment): self
    {
        return self::create([
            'project_id' => $project->id,
            'user_id' => $user->id,
            'activity_type' => 'comment',
            'stage_context' => $project->current_stage,
            'description' => $comment,
        ]);
    }

    /**
     * Create comment activity for asset
     */
    public static function logAssetComment(ProjectAsset $asset, User $user, string $comment): self
    {
        return self::create([
            'project_id' => $asset->project_id,
            'project_asset_id' => $asset->id,
            'user_id' => $user->id,
            'activity_type' => 'comment',
            'stage_context' => $asset->current_stage,
            'description' => $comment,
        ]);
    }

    /**
     * Create approval activity
     */
    public static function logApproval(ProjectKanban $project, User $user, string $stage, bool $approved, ?string $comments = null): self
    {
        return self::create([
            'project_id' => $project->id,
            'user_id' => $user->id,
            'activity_type' => $approved ? 'approval' : 'rejection',
            'stage_context' => $stage,
            'description' => $comments ?? ($approved ? 'Menyetujui' : 'Menolak'),
        ]);
    }

    /**
     * Create upload activity
     */
    public static function logUpload(ProjectKanban $project, User $user, string $fileName): self
    {
        return self::create([
            'project_id' => $project->id,
            'user_id' => $user->id,
            'activity_type' => 'upload',
            'stage_context' => $project->current_stage,
            'description' => "Mengupload file: {$fileName}",
        ]);
    }

    /**
     * Create upload activity for asset
     */
    public static function logAssetUpload(ProjectAsset $asset, User $user, string $fileName): self
    {
        return self::create([
            'project_id' => $asset->project_id,
            'project_asset_id' => $asset->id,
            'user_id' => $user->id,
            'activity_type' => 'upload',
            'stage_context' => $asset->current_stage,
            'description' => "Mengupload file: {$fileName}",
        ]);
    }

    /**
     * Create obstacle activity
     */
    public static function logObstacle(ProjectKanban $project, User $user, string $description): self
    {
        return self::create([
            'project_id' => $project->id,
            'user_id' => $user->id,
            'activity_type' => 'obstacle',
            'stage_context' => $project->current_stage,
            'description' => $description,
        ]);
    }

    /**
     * Create asset created activity
     */
    public static function logAssetCreated(ProjectAsset $asset, User $user): self
    {
        return self::create([
            'project_id' => $asset->project_id,
            'project_asset_id' => $asset->id,
            'user_id' => $user->id,
            'activity_type' => 'asset_created',
            'stage_context' => $asset->current_stage,
            'description' => "Menambahkan objek: {$asset->name}",
        ]);
    }

    /**
     * Create asset completed activity
     */
    public static function logAssetCompleted(ProjectAsset $asset, User $user): self
    {
        return self::create([
            'project_id' => $asset->project_id,
            'project_asset_id' => $asset->id,
            'user_id' => $user->id,
            'activity_type' => 'asset_completed',
            'stage_context' => 'done',
            'description' => "Objek '{$asset->name}' telah selesai",
        ]);
    }

    /**
     * Scope by activity type
     */
    public function scopeOfType($query, string $type)
    {
        return $query->where('activity_type', $type);
    }

    /**
     * Scope for project level activities (no asset)
     */
    public function scopeProjectLevel($query)
    {
        return $query->whereNull('project_asset_id');
    }

    /**
     * Scope for asset specific activities
     */
    public function scopeAssetLevel($query)
    {
        return $query->whereNotNull('project_asset_id');
    }
}

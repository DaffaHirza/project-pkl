<?php

namespace App\Services;

use App\Models\User;
use App\Models\Notification;
use App\Models\ProjectAssetKanban;
use App\Models\AssetNoteKanban;
use App\Notifications\AssessmentUpdated;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

class KanbanNotificationService
{
    /**
     * Send Telegram notification to all eligible users
     * Handles deduplication by telegram_chat_id to prevent duplicate notifications
     */
    private static function sendTelegramNotification(
        string $type,
        array $data,
        ?int $excludeUserId = null
    ): void {
        try {
            // Get users with telegram_chat_id, excluding the actor, and deduplicate by chat_id
            $query = User::whereNotNull('telegram_chat_id')
                ->where('telegram_chat_id', '!=', '');
            
            if ($excludeUserId) {
                $query->where('id', '!=', $excludeUserId);
            }
            
            $users = $query->get()->unique('telegram_chat_id');
            
            foreach ($users as $user) {
                $user->notify(new AssessmentUpdated($type, $data));
            }
            
            Log::info("Telegram notifications sent", [
                'type' => $type,
                'recipients_count' => $users->count(),
            ]);
        } catch (\Exception $e) {
            Log::error("Failed to send Telegram notification", [
                'type' => $type,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Send notification when asset stage is changed
     */
    public static function notifyStageChange(
        ProjectAssetKanban $asset,
        int $oldStage,
        int $newStage,
        User $changedBy,
        ?string $note = null
    ): void {
        $oldStageName = ProjectAssetKanban::STAGES[$oldStage] ?? 'Unknown';
        $newStageName = ProjectAssetKanban::STAGES[$newStage] ?? 'Unknown';
        
        $data = [
            'title' => 'Stage Asset Berubah',
            'message' => "{$changedBy->name} memindahkan '{$asset->name}' dari {$oldStageName} ke {$newStageName}",
            'asset_id' => $asset->id,
            'asset_name' => $asset->name,
            'asset_code' => $asset->asset_code,
            'project_id' => $asset->project_id,
            'old_stage' => $oldStage,
            'new_stage' => $newStage,
            'changed_by' => $changedBy->id,
            'note' => $note,
            'action_url' => route('kanban.assets.show', $asset->id),
        ];

        // Notify all admins
        $admins = User::where('id', '!=', $changedBy->id)->get();
        
        foreach ($admins as $admin) {
            Notification::notify($admin, 'asset_stage_changed', $data);
        }
    }

    /**
     * Send notification when document is uploaded
     */
    public static function notifyDocumentUploaded(
        ProjectAssetKanban $asset,
        string $fileName,
        User $uploadedBy
    ): void {
        $data = [
            'title' => 'Dokumen Baru Diupload',
            'message' => "{$uploadedBy->name} mengupload '{$fileName}' pada asset '{$asset->name}'",
            'asset_id' => $asset->id,
            'asset_name' => $asset->name,
            'file_name' => $fileName,
            'uploaded_by' => $uploadedBy->id,
            'action_url' => route('kanban.assets.show', $asset->id),
        ];

        $admins = User::where('id', '!=', $uploadedBy->id)->get();
        
        foreach ($admins as $admin) {
            Notification::notify($admin, 'asset_document_uploaded', $data);
        }
    }

    /**
     * Send notification when note/comment is added
     */
    public static function notifyNoteAdded(
        ProjectAssetKanban $asset,
        AssetNoteKanban $note,
        User $addedBy
    ): void {
        $data = [
            'title' => 'Catatan Baru',
            'message' => "{$addedBy->name} menambahkan catatan pada asset '{$asset->name}'",
            'asset_id' => $asset->id,
            'asset_name' => $asset->name,
            'note_id' => $note->id,
            'note_content' => Str::limit($note->content, 100),
            'added_by' => $addedBy->id,
            'action_url' => route('kanban.assets.show', $asset->id),
        ];

        $admins = User::where('id', '!=', $addedBy->id)->get();
        
        foreach ($admins as $admin) {
            Notification::notify($admin, 'asset_note_added', $data);
        }
    }

    /**
     * Send notification when asset is created
     */
    public static function notifyAssetCreated(
        ProjectAssetKanban $asset,
        User $createdBy
    ): void {
        $data = [
            'title' => 'Asset Baru Dibuat',
            'message' => "{$createdBy->name} membuat asset baru '{$asset->name}'",
            'asset_id' => $asset->id,
            'asset_name' => $asset->name,
            'asset_code' => $asset->asset_code,
            'project_id' => $asset->project_id,
            'created_by' => $createdBy->id,
            'action_url' => route('kanban.assets.show', $asset->id),
        ];

        $admins = User::where('id', '!=', $createdBy->id)->get();
        
        foreach ($admins as $admin) {
            Notification::notify($admin, 'asset_created', $data);
        }
    }

    /**
     * Send notification when project is created
     */
    public static function notifyProjectCreated(
        $project,
        User $createdBy
    ): void {
        $data = [
            'title' => 'Project Baru Dibuat',
            'message' => "{$createdBy->name} membuat project baru '{$project->name}'",
            'project_id' => $project->id,
            'project_name' => $project->name,
            'project_code' => $project->project_code,
            'created_by' => $createdBy->id,
            'action_url' => route('kanban.projects.show', $project->id),
        ];

        $admins = User::where('id', '!=', $createdBy->id)->get();
        
        foreach ($admins as $admin) {
            Notification::notify($admin, 'project_created', $data);
        }
    }

    /**
     * Send notification when priority changes to critical
     */
    public static function notifyPriorityCritical(
        ProjectAssetKanban $asset,
        User $changedBy
    ): void {
        $data = [
            'title' => 'Priority Critical!',
            'message' => "Asset '{$asset->name}' ditandai sebagai CRITICAL oleh {$changedBy->name}",
            'asset_id' => $asset->id,
            'asset_name' => $asset->name,
            'changed_by' => $changedBy->id,
            'action_url' => route('kanban.assets.show', $asset->id),
        ];

        $admins = User::where('id', '!=', $changedBy->id)->get();
        
        foreach ($admins as $admin) {
            Notification::notify($admin, 'asset_priority_critical', $data);
        }
    }
}

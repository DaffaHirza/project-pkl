<?php

namespace App\Services;

use App\Models\User;
use App\Models\Notification;
use App\Models\AssetKanban;
use App\Models\AssetNoteKanban;
use App\Notifications\AssessmentUpdated;
use Illuminate\Support\Str;

class KanbanNotificationService
{
    /**
     * Send notification when asset stage is changed
     */
    public static function notifyStageChange(
        AssetKanban $asset,
        int $oldStage,
        int $newStage,
        User $changedBy,
        ?string $note = null
    ): void {
        $oldStageName = AssetKanban::STAGES[$oldStage] ?? 'Unknown';
        $newStageName = AssetKanban::STAGES[$newStage] ?? 'Unknown';
        
        $data = [
            'title' => 'Stage Asset Berubah',
            'message' => "{$changedBy->name} memindahkan '{$asset->name}' dari {$oldStageName} ke {$newStageName}",
            'asset_id' => $asset->id,
            'asset_name' => $asset->name,
            'client_id' => $asset->client_id,
            'old_stage' => $oldStage,
            'new_stage' => $newStage,
            'changed_by' => $changedBy->id,
            'note' => $note,
            'action_url' => route('kanban.assets.show', $asset->id),
        ];

        // Database notification - exclude self (user doesn't need in-app notif for own action)
        $otherUsers = User::where('id', '!=', $changedBy->id)->get();
        foreach ($otherUsers as $user) {
            Notification::notify($user, 'asset_stage_changed', $data);
        }

        // Telegram notification - send to ALL users with telegram_chat_id (including self)
        $telegramUsers = User::whereNotNull('telegram_chat_id')
            ->where('telegram_chat_id', '!=', '')
            ->get()
            ->unique('telegram_chat_id'); // Deduplicate same chat_id
        
        foreach ($telegramUsers as $user) {
            $user->notify(new AssessmentUpdated($asset, 'stage_change', $changedBy, $note));
        }
    }

    /**
     * Send notification when document is uploaded
     */
    public static function notifyDocumentUploaded(
        AssetKanban $asset,
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

        $users = User::where('id', '!=', $uploadedBy->id)->get();
        
        foreach ($users as $user) {
            // Database notification only (no Telegram for document uploads)
            Notification::notify($user, 'asset_document_uploaded', $data);
        }
    }

    /**
     * Send notification when note/comment is added
     */
    public static function notifyNoteAdded(
        AssetKanban $asset,
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

        // Database notification - exclude self
        $otherUsers = User::where('id', '!=', $addedBy->id)->get();
        foreach ($otherUsers as $user) {
            Notification::notify($user, 'asset_note_added', $data);
        }

        // Telegram notification - send to ALL users with telegram_chat_id (including self)
        $telegramUsers = User::whereNotNull('telegram_chat_id')
            ->where('telegram_chat_id', '!=', '')
            ->get()
            ->unique('telegram_chat_id');
        
        foreach ($telegramUsers as $user) {
            $user->notify(new AssessmentUpdated($asset, 'new_note', $addedBy, $note->content));
        }
    }

    /**
     * Send notification when asset is created
     */
    public static function notifyAssetCreated(
        AssetKanban $asset,
        User $createdBy
    ): void {
        $data = [
            'title' => 'Asset Baru Dibuat',
            'message' => "{$createdBy->name} membuat asset baru '{$asset->name}'",
            'asset_id' => $asset->id,
            'asset_name' => $asset->name,
            'client_id' => $asset->client_id,
            'created_by' => $createdBy->id,
            'action_url' => route('kanban.assets.show', $asset->id),
        ];

        $users = User::where('id', '!=', $createdBy->id)->get();
        
        foreach ($users as $user) {
            Notification::notify($user, 'asset_created', $data);
        }
    }

    /**
     * Send notification when client is created
     */
    public static function notifyClientCreated(
        $client,
        User $createdBy
    ): void {
        $data = [
            'title' => 'Client Baru Dibuat',
            'message' => "{$createdBy->name} membuat client baru '{$client->display_name}'",
            'client_id' => $client->id,
            'client_name' => $client->display_name,
            'created_by' => $createdBy->id,
            'action_url' => route('kanban.clients.show', $client->id),
        ];

        $users = User::where('id', '!=', $createdBy->id)->get();
        
        foreach ($users as $user) {
            Notification::notify($user, 'client_created', $data);
        }
    }

    /**
     * Send notification when priority changes to critical
     */
    public static function notifyPriorityCritical(
        AssetKanban $asset,
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

        $users = User::where('id', '!=', $changedBy->id)->get();
        
        foreach ($users as $user) {
            // Database notification only (no Telegram for priority changes)
            Notification::notify($user, 'asset_priority_critical', $data);
        }
    }
}

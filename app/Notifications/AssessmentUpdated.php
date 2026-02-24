<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Str;
use NotificationChannels\Telegram\TelegramChannel;
use NotificationChannels\Telegram\TelegramMessage;
use App\Models\ProjectAssetKanban;
use App\Models\User;

class AssessmentUpdated extends Notification implements ShouldQueue
{
    use Queueable;

    protected ProjectAssetKanban $asset;
    protected string $type; // 'stage_change', 'new_note', 'document_uploaded'
    protected User $actor;
    protected ?string $additionalInfo;

    /**
     * Create a new notification instance.
     */
    public function __construct(ProjectAssetKanban $asset, string $type, User $actor, ?string $additionalInfo = null)
    {
        $this->asset = $asset;
        $this->type = $type;
        $this->actor = $actor;
        $this->additionalInfo = $additionalInfo;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        $channels = ['database'];
        
        // Only send Telegram if user has telegram_chat_id configured
        if (!empty($notifiable->telegram_chat_id)) {
            $channels[] = TelegramChannel::class;
        }
        
        return $channels;
    }

    /**
     * Get the Telegram representation of the notification.
     */
    public function toTelegram(object $notifiable): TelegramMessage
    {
        $url = url("/kanban/assets/{$this->asset->id}");
        $stageName = ProjectAssetKanban::STAGES[$this->asset->current_stage] ?? 'Unknown';
        
        switch ($this->type) {
            case 'stage_change':
                $emoji = "🔄";
                $title = "Status Berubah!";
                $content = "Asset *{$this->asset->name}* ({$this->asset->asset_code}) kini berada di stage: *{$stageName}*.";
                if ($this->additionalInfo) {
                    $content .= "\n\n📝 Catatan: " . Str::limit($this->additionalInfo, 100);
                }
                break;
                
            case 'new_note':
                $emoji = "📝";
                $title = "Catatan Baru!";
                $content = "{$this->actor->name} menambahkan catatan pada *{$this->asset->name}*.";
                if ($this->additionalInfo) {
                    $content .= "\n\n💬 \"" . Str::limit($this->additionalInfo, 100) . "\"";
                }
                break;
                
            case 'document_uploaded':
                $emoji = "📎";
                $title = "Dokumen Baru!";
                $content = "{$this->actor->name} mengupload dokumen pada *{$this->asset->name}*.";
                if ($this->additionalInfo) {
                    $content .= "\n\n📄 File: {$this->additionalInfo}";
                }
                break;
                
            case 'priority_change':
                $emoji = "⚠️";
                $title = "Prioritas Berubah!";
                $priorityName = ProjectAssetKanban::PRIORITIES[$this->asset->priority] ?? $this->asset->priority;
                $content = "Asset *{$this->asset->name}* kini memiliki prioritas: *{$priorityName}*.";
                break;
                
            default:
                $emoji = "🔔";
                $title = "Update Asset";
                $content = "Ada update pada asset *{$this->asset->name}*.";
        }

        $message = TelegramMessage::create()
            ->to($notifiable->telegram_chat_id)
            ->content("{$emoji} *{$title}*\n\nHalo {$notifiable->name},\n{$content}\n\n🏢 Project: {$this->asset->project->name}\n\n📋 Kode Asset: {$this->asset->asset_code}");
        
        // Only add button if URL is not localhost (Telegram rejects localhost URLs)
        $appUrl = config('app.url');
        if (!str_contains($appUrl, 'localhost') && !str_contains($appUrl, '127.0.0.1')) {
            $message->button('Buka Aplikasi', $url);
        }
        
        return $message;
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'type' => $this->type,
            'asset_id' => $this->asset->id,
            'asset_name' => $this->asset->name,
            'asset_code' => $this->asset->asset_code,
            'project_id' => $this->asset->project_id,
            'actor_id' => $this->actor->id,
            'actor_name' => $this->actor->name,
            'additional_info' => $this->additionalInfo,
            'action_url' => url("/kanban/assets/{$this->asset->id}"),
        ];
    }
}

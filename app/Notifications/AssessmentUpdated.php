<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Str;
use NotificationChannels\Telegram\TelegramChannel;
use NotificationChannels\Telegram\TelegramMessage;
use App\Models\AssetKanban;
use App\Models\User;

class AssessmentUpdated extends Notification implements ShouldQueue
{
    use Queueable;

    protected AssetKanban $asset;
    protected string $type; // 'stage_change', 'new_note', 'document_uploaded'
    protected User $actor;
    protected ?string $additionalInfo;

    /**
     * Create a new notification instance.
     */
    public function __construct(AssetKanban $asset, string $type, User $actor, ?string $additionalInfo = null)
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
        // Only Telegram channel - database notification is handled by Notification model
        if (!empty($notifiable->telegram_chat_id)) {
            return [TelegramChannel::class];
        }
        
        return [];
    }

    /**
     * Get the Telegram representation of the notification.
     */
    public function toTelegram(object $notifiable): TelegramMessage
    {
        // Guard: ensure telegram_chat_id exists
        if (empty($notifiable->telegram_chat_id)) {
            throw new \RuntimeException("User {$notifiable->id} has no telegram_chat_id");
        }
        
        // Use config('app.url') instead of url() helper for queue context
        $appUrl = config('app.url');
        $assetUrl = rtrim($appUrl, '/') . "/kanban/assets/{$this->asset->id}";
        $stageName = AssetKanban::STAGES[$this->asset->current_stage] ?? 'Unknown';
        
        switch ($this->type) {
            case 'stage_change':
                $title = "Status Telah Berubah";
                $content = "*{$this->actor->name}* memindahkan asset *{$this->asset->name}* ke stage: *{$stageName}*.";
                if ($this->additionalInfo) {
                    $content .= "\n\nCatatan: " . Str::limit($this->additionalInfo, 100);
                }
                break;
                
            case 'new_note':
                $title = "Ada Catatan Baru!";
                $content = "{$this->actor->name} menambahkan catatan pada *{$this->asset->name}*.";
                if ($this->additionalInfo) {
                    $content .= "\n\n\"" . Str::limit($this->additionalInfo, 100) . "\"";
                }
                break;
                
            case 'document_uploaded':
                $title = "Dokumen Baru!";
                $content = "{$this->actor->name} mengupload dokumen pada *{$this->asset->name}*.";
                if ($this->additionalInfo) {
                    $content .= "\n\n📄 File: {$this->additionalInfo}";
                }
                break;
                
            default:
                $title = "Update Asset";
                $content = "*{$this->actor->name}* melakukan update pada asset *{$this->asset->name}*.";
        }

        $clientName = $this->asset->client->display_name ?? 'Unknown';
        $message = TelegramMessage::create()
            ->to($notifiable->telegram_chat_id)
            ->content("*{$title}*\n\nHi kamu {$notifiable->name},\n{$content}\n\nClient: {$clientName}");
        
        // Only add button if URL is not localhost (Telegram rejects localhost URLs)
        if (!str_contains($appUrl, 'localhost') && !str_contains($appUrl, '127.0.0.1')) {
            $message->button('Buka Aplikasi', $assetUrl);
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
        $appUrl = rtrim(config('app.url'), '/');
        
        return [
            'type' => $this->type,
            'asset_id' => $this->asset->id,
            'asset_name' => $this->asset->name,
            'client_id' => $this->asset->client_id,
            'actor_id' => $this->actor->id,
            'actor_name' => $this->actor->name,
            'additional_info' => $this->additionalInfo,
            'action_url' => "{$appUrl}/kanban/assets/{$this->asset->id}",
        ];
    }
}

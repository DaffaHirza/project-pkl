<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Notification extends Model
{
    use HasUuids;

    /**
     * The primary key type
     */
    protected $keyType = 'string';

    /**
     * Indicates if the IDs are auto-incrementing.
     */
    public $incrementing = false;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'id',
        'type',
        'notifiable_type',
        'notifiable_id',
        'data',
        'read_at',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'data' => 'array',
        'read_at' => 'datetime',
    ];

    /**
     * Notification types - hanya yang digunakan dalam sistem
     */
    public const TYPES = [
        // Asset types
        'asset_stage_changed' => 'Stage Asset Berubah',
        'asset_created' => 'Asset Baru Dibuat',
        'asset_document_uploaded' => 'Dokumen Asset Diupload',
        'asset_note_added' => 'Catatan Asset Ditambahkan',
        'asset_priority_critical' => 'Asset Priority Critical',
        
        // Client types
        'client_created' => 'Client Baru Ditambahkan',
        
        // System
        'system' => 'Sistem',
    ];

    /**
     * Get the notifiable entity (user, team, etc.)
     */
    public function notifiable()
    {
        return $this->morphTo();
    }

    /**
     * Check if notification has been read
     */
    public function isRead(): bool
    {
        return $this->read_at !== null;
    }

    /**
     * Check if notification is unread
     */
    public function isUnread(): bool
    {
        return $this->read_at === null;
    }

    /**
     * Mark the notification as read
     */
    public function markAsRead(): void
    {
        if ($this->isUnread()) {
            $this->update(['read_at' => now()]);
        }
    }

    /**
     * Mark the notification as unread
     */
    public function markAsUnread(): void
    {
        $this->update(['read_at' => null]);
    }

    /**
     * Get the notification title from data
     */
    public function getTitleAttribute(): string
    {
        return $this->data['title'] ?? self::TYPES[$this->type] ?? 'Notifikasi';
    }

    /**
     * Get the notification message from data
     */
    public function getMessageAttribute(): string
    {
        return $this->data['message'] ?? '';
    }

    /**
     * Get the notification icon based on type
     */
    public function getIconAttribute(): string
    {
        $icons = [
            'asset_stage_changed' => 'git-branch',
            'asset_created' => 'plus-circle',
            'asset_document_uploaded' => 'upload',
            'asset_note_added' => 'message-circle',
            'asset_priority_critical' => 'alert-triangle',
            'client_created' => 'users',
            'system' => 'info',
        ];

        return $icons[$this->type] ?? 'bell';
    }

    /**
     * Get the notification color based on type
     */
    public function getColorAttribute(): string
    {
        $colors = [
            'asset_stage_changed' => 'purple',
            'asset_created' => 'green',
            'asset_document_uploaded' => 'blue',
            'asset_note_added' => 'gray',
            'asset_priority_critical' => 'red',
            'client_created' => 'blue',
            'system' => 'gray',
        ];

        return $colors[$this->type] ?? 'gray';
    }

    /**
     * Get the action URL from data
     */
    public function getActionUrlAttribute(): ?string
    {
        return $this->data['action_url'] ?? null;
    }

    /**
     * Scope for unread notifications
     */
    public function scopeUnread($query)
    {
        return $query->whereNull('read_at');
    }

    /**
     * Scope for read notifications
     */
    public function scopeRead($query)
    {
        return $query->whereNotNull('read_at');
    }

    /**
     * Scope for recent notifications (last 30 days)
     */
    public function scopeRecent($query)
    {
        return $query->where('created_at', '>=', now()->subDays(30));
    }

    /**
     * Scope by type
     */
    public function scopeOfType($query, string $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Create notification for a user
     */
    public static function notify($user, string $type, array $data): self
    {
        return self::create([
            'type' => $type,
            'notifiable_type' => get_class($user),
            'notifiable_id' => $user->id,
            'data' => $data,
        ]);
    }

    /**
     * Create notifications for multiple users
     */
    public static function notifyMany($users, string $type, array $data): void
    {
        foreach ($users as $user) {
            self::notify($user, $type, $data);
        }
    }
}

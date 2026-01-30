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
     * Notification types
     */
    public const TYPES = [
        'card_assigned' => 'Ditugaskan ke Card',
        'card_due_soon' => 'Deadline Mendekat',
        'card_overdue' => 'Card Overdue',
        'card_comment' => 'Komentar Baru',
        'card_attachment' => 'Attachment Baru',
        'card_moved' => 'Card Dipindahkan',
        'project_stage_changed' => 'Stage Project Berubah',
        'project_assigned' => 'Ditugaskan ke Project',
        'inspection_scheduled' => 'Jadwal Inspeksi',
        'inspection_reminder' => 'Pengingat Inspeksi',
        'approval_required' => 'Perlu Approval',
        'approval_completed' => 'Approval Selesai',
        'invoice_created' => 'Invoice Dibuat',
        'invoice_paid' => 'Invoice Dibayar',
        'invoice_overdue' => 'Invoice Overdue',
        'report_uploaded' => 'Laporan Diupload',
        'report_revision' => 'Permintaan Revisi',
        'obstacle_reported' => 'Halangan Dilaporkan',
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
            'card_assigned' => 'user-plus',
            'card_due_soon' => 'clock',
            'card_overdue' => 'alert-triangle',
            'card_comment' => 'message-circle',
            'card_attachment' => 'paperclip',
            'card_moved' => 'move',
            'project_stage_changed' => 'git-branch',
            'project_assigned' => 'briefcase',
            'inspection_scheduled' => 'calendar',
            'inspection_reminder' => 'bell',
            'approval_required' => 'check-circle',
            'approval_completed' => 'check-square',
            'invoice_created' => 'file-text',
            'invoice_paid' => 'dollar-sign',
            'invoice_overdue' => 'alert-circle',
            'report_uploaded' => 'upload',
            'report_revision' => 'edit',
            'obstacle_reported' => 'alert-octagon',
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
            'card_assigned' => 'blue',
            'card_due_soon' => 'yellow',
            'card_overdue' => 'red',
            'card_comment' => 'gray',
            'card_attachment' => 'gray',
            'card_moved' => 'blue',
            'project_stage_changed' => 'purple',
            'project_assigned' => 'blue',
            'inspection_scheduled' => 'green',
            'inspection_reminder' => 'yellow',
            'approval_required' => 'orange',
            'approval_completed' => 'green',
            'invoice_created' => 'blue',
            'invoice_paid' => 'green',
            'invoice_overdue' => 'red',
            'report_uploaded' => 'blue',
            'report_revision' => 'yellow',
            'obstacle_reported' => 'red',
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

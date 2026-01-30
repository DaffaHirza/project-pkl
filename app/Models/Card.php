<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Card extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['column_id', 'title', 'description', 'order', 'priority', 'due_date'];

    protected $casts = [
        'due_date' => 'date',
    ];

    /**
     * Priority options
     */
    public const PRIORITIES = [
        'low' => 'Rendah',
        'medium' => 'Sedang',
        'high' => 'Tinggi',
    ];

    /**
     * Get the column this card belongs to
     */
    public function column()
    {
        return $this->belongsTo(Column::class);
    }

    /**
     * Get the board this card belongs to through column
     */
    public function board()
    {
        return $this->hasOneThrough(
            Board::class,
            Column::class,
            'id',           // Foreign key on columns table
            'id',           // Foreign key on boards table
            'column_id',    // Local key on cards table
            'board_id'      // Local key on columns table
        );
    }

    /**
     * Get all users assigned to this card
     */
    public function assignedUsers()
    {
        return $this->belongsToMany(User::class, 'card_assignments');
    }

    /**
     * Get all assignments for this card
     */
    public function assignments()
    {
        return $this->hasMany(CardAssignment::class);
    }

    /**
     * Get all attachments for this card
     */
    public function attachments()
    {
        return $this->hasMany(CardAttachment::class);
    }

    /**
     * Get priority label
     */
    public function getPriorityLabelAttribute(): string
    {
        return self::PRIORITIES[$this->priority] ?? $this->priority;
    }

    /**
     * Check if card is overdue
     */
    public function isOverdue(): bool
    {
        return $this->due_date && $this->due_date->isPast();
    }

    /**
     * Check if card is due soon (within 3 days)
     */
    public function isDueSoon(): bool
    {
        return $this->due_date && $this->due_date->isBetween(now(), now()->addDays(3));
    }

    /**
     * Scope for high priority cards
     */
    public function scopeHighPriority($query)
    {
        return $query->where('priority', 'high');
    }

    /**
     * Scope for overdue cards
     */
    public function scopeOverdue($query)
    {
        return $query->whereNotNull('due_date')
            ->where('due_date', '<', now());
    }

    /**
     * Scope for cards due soon
     */
    public function scopeDueSoon($query)
    {
        return $query->whereNotNull('due_date')
            ->whereBetween('due_date', [now(), now()->addDays(3)]);
    }

    /**
     * Move card to another column
     */
    public function moveToColumn(Column $column, int $order = null): self
    {
        $this->update([
            'column_id' => $column->id,
            'order' => $order ?? $column->cards()->count(),
        ]);

        return $this;
    }

    /**
     * Assign user to this card
     */
    public function assignUser(User $user, User $assignedBy = null): CardAssignment
    {
        return $this->assignments()->create([
            'user_id' => $user->id,
            'assigned_by' => $assignedBy?->id,
        ]);
    }

    /**
     * Remove user from this card
     */
    public function unassignUser(User $user): bool
    {
        return $this->assignments()
            ->where('user_id', $user->id)
            ->delete();
    }
}

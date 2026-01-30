<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CardAssignment extends Model
{
    use HasFactory;

    protected $fillable = ['card_id', 'user_id', 'assigned_by'];

    /**
     * Get the card this assignment belongs to
     */
    public function card()
    {
        return $this->belongsTo(Card::class);
    }

    /**
     * Get the user assigned to this card
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the user who made this assignment
     */
    public function assignedBy()
    {
        return $this->belongsTo(User::class, 'assigned_by');
    }

    /**
     * Scope for assignments by a specific user
     */
    public function scopeAssignedTo($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Check if assignment is for a specific user
     */
    public function isFor(User $user): bool
    {
        return $this->user_id === $user->id;
    }
}

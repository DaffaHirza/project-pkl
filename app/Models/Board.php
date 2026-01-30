<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Board extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['name', 'description', 'created_by'];

    /**
     * Get the user who created this board
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get all columns in this board
     */
    public function columns()
    {
        return $this->hasMany(Column::class)->orderBy('order');
    }

    /**
     * Get all cards in this board through columns
     */
    public function cards()
    {
        return $this->hasManyThrough(Card::class, Column::class);
    }

    /**
     * Get total cards count
     */
    public function getTotalCardsAttribute(): int
    {
        return $this->cards()->count();
    }

    /**
     * Get overdue cards count
     */
    public function getOverdueCardsCountAttribute(): int
    {
        return $this->cards()
            ->whereNotNull('due_date')
            ->where('due_date', '<', now())
            ->count();
    }

    /**
     * Get high priority cards count
     */
    public function getHighPriorityCardsCountAttribute(): int
    {
        return $this->cards()->where('priority', 'high')->count();
    }

    /**
     * Create default columns for a new board
     */
    public function createDefaultColumns(): void
    {
        $defaultColumns = [
            ['name' => 'To Do', 'order' => 0],
            ['name' => 'In Progress', 'order' => 1],
            ['name' => 'Done', 'order' => 2],
        ];

        foreach ($defaultColumns as $column) {
            $this->columns()->create($column);
        }
    }

    /**
     * Scope for boards created by a user
     */
    public function scopeCreatedBy($query, $userId)
    {
        return $query->where('created_by', $userId);
    }
}

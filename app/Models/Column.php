<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Column extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['board_id', 'name', 'order', 'color'];

    /**
     * Get the board this column belongs to
     */
    public function board()
    {
        return $this->belongsTo(Board::class);
    }

    /**
     * Get all cards in this column
     */
    public function cards()
    {
        return $this->hasMany(Card::class)->orderBy('order');
    }

    /**
     * Get cards count
     */
    public function getCardsCountAttribute(): int
    {
        return $this->cards()->count();
    }

    /**
     * Check if column is empty
     */
    public function isEmpty(): bool
    {
        return $this->cards()->count() === 0;
    }

    /**
     * Move card to this column
     */
    public function moveCardHere(Card $card, int $order = null): Card
    {
        $card->update([
            'column_id' => $this->id,
            'order' => $order ?? $this->cards()->count(),
        ]);

        return $card;
    }

    /**
     * Reorder cards in this column
     */
    public function reorderCards(array $cardIds): void
    {
        foreach ($cardIds as $order => $cardId) {
            Card::where('id', $cardId)
                ->where('column_id', $this->id)
                ->update(['order' => $order]);
        }
    }
}

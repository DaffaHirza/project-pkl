<?php

namespace App\Http\Controllers;

use App\Models\Card;
use App\Models\Column;
use App\Models\CardAssignment;
use App\Models\User;
use Illuminate\Http\Request;

class CardController extends Controller
{
    /**
     * Create a new card in a column
     */
    public function store(Request $request, Column $column)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'priority' => 'nullable|in:low,medium,high',
            'due_date' => 'nullable|date',
        ]);

        $card = Card::create([
            ...$validated,
            'column_id' => $column->id,
            'order' => $column->cards()->count(),
        ]);

        return back()->with('success', 'Card created successfully!');
    }

    /**
     * Create a new card from board (with column_id in request)
     */
    public function storeFromBoard(Request $request, $board)
    {
        $validated = $request->validate([
            'column_id' => 'required|exists:columns,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'priority' => 'nullable|in:low,medium,high',
            'due_date' => 'nullable|date',
            'attachments' => 'nullable|array',
            'attachments.*' => 'file|max:51200', // 50MB max per file
        ]);

        $column = Column::findOrFail($validated['column_id']);
        
        $card = Card::create([
            'title' => $validated['title'],
            'description' => $validated['description'],
            'priority' => $validated['priority'],
            'due_date' => $validated['due_date'],
            'column_id' => $column->id,
            'order' => $column->cards()->count(),
        ]);

        // Handle file attachments if present
        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                $fileName = $file->getClientOriginalName();
                $filePath = $file->store('card-attachments', 'public');
                
                $card->attachments()->create([
                    'file_name' => $fileName,
                    'file_path' => $filePath,
                    'mime_type' => $file->getMimeType(),
                    'file_size' => $file->getSize(),
                ]);
            }
        }

        return back()->with('success', 'Tugas berhasil ditambahkan!');
    }

    /**
     * Update a card
     */
    public function update(Request $request, Card $card)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'priority' => 'nullable|in:low,medium,high',
            'due_date' => 'nullable|date',
        ]);

        $card->update($validated);

        return back()->with('success', 'Card updated successfully!');
    }

    /**
     * Move card to another column (for drag & drop)
     */
    public function move(Request $request, Card $card)
    {
        $validated = $request->validate([
            'column_id' => 'required|exists:columns,id',
            'order' => 'required|integer|min:0',
        ]);

        // Update order of other cards in the destination column
        Card::where('column_id', $validated['column_id'])
            ->where('id', '!=', $card->id)
            ->where('order', '>=', $validated['order'])
            ->increment('order');

        // Update the card
        $card->update([
            'column_id' => $validated['column_id'],
            'order' => $validated['order'],
        ]);

        return response()->json(['success' => true]);
    }

    /**
     * Assign user(s) to a card
     */
    public function assignUsers(Request $request, Card $card)
    {
        $validated = $request->validate([
            'user_ids' => 'required|array',
            'user_ids.*' => 'exists:users,id',
        ]);

        // Clear existing assignments
        $card->assignments()->delete();

        // Create new assignments
        foreach ($validated['user_ids'] as $userId) {
            CardAssignment::create([
                'card_id' => $card->id,
                'user_id' => $userId,
            ]);
        }

        return back()->with('success', 'Users assigned to card!');
    }

    /**
     * Remove user from a card
     */
    public function removeUser(CardAssignment $assignment)
    {
        $assignment->delete();
        return back()->with('success', 'User removed from card!');
    }

    /**
     * Delete a card
     */
    public function destroy(Card $card)
    {
        $boardId = $card->column->board->id;
        $card->delete();

        return back()->with('success', 'Card deleted successfully!');
    }
}

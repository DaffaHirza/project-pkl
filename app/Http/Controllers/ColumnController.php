<?php

namespace App\Http\Controllers;

use App\Models\Board;
use App\Models\Column;
use Illuminate\Http\Request;

class ColumnController extends Controller
{
    /**
     * Store a newly created column in the board
     */
    public function store(Request $request, Board $board)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'color' => 'nullable|string|max:50',
        ]);

        $column = Column::create([
            'board_id' => $board->id,
            'name' => $validated['name'],
            'color' => $validated['color'] ?? null,
            'order' => $board->columns()->count(),
        ]);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Kolom berhasil ditambahkan!',
                'column' => $column,
            ]);
        }

        return back()->with('success', 'Kolom berhasil ditambahkan!');
    }

    /**
     * Update the specified column
     */
    public function update(Request $request, Column $column)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'color' => 'nullable|string|max:50',
        ]);

        $column->update($validated);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Kolom berhasil diperbarui!',
                'column' => $column,
            ]);
        }

        return back()->with('success', 'Kolom berhasil diperbarui!');
    }

    /**
     * Move/reorder the column (for drag & drop)
     */
    public function move(Request $request, Column $column)
    {
        $validated = $request->validate([
            'order' => 'required|integer|min:0',
        ]);

        $board = $column->board;
        $newOrder = $validated['order'];
        $oldOrder = $column->order;

        if ($newOrder !== $oldOrder) {
            if ($newOrder > $oldOrder) {
                // Moving down: decrease order of columns in between
                Column::where('board_id', $board->id)
                    ->where('order', '>', $oldOrder)
                    ->where('order', '<=', $newOrder)
                    ->decrement('order');
            } else {
                // Moving up: increase order of columns in between
                Column::where('board_id', $board->id)
                    ->where('order', '>=', $newOrder)
                    ->where('order', '<', $oldOrder)
                    ->increment('order');
            }

            $column->update(['order' => $newOrder]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Urutan kolom berhasil diubah!',
        ]);
    }

    /**
     * Reorder all columns at once
     */
    public function reorder(Request $request, Board $board)
    {
        $validated = $request->validate([
            'columns' => 'required|array',
            'columns.*' => 'exists:columns,id',
        ]);

        foreach ($validated['columns'] as $order => $columnId) {
            Column::where('id', $columnId)
                ->where('board_id', $board->id)
                ->update(['order' => $order]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Urutan kolom berhasil diubah!',
        ]);
    }

    /**
     * Remove the specified column
     */
    public function destroy(Request $request, Column $column)
    {
        $board = $column->board;
        $deletedOrder = $column->order;

        // Check if column has cards
        if ($column->cards()->exists()) {
            if ($request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Kolom tidak bisa dihapus karena masih memiliki kartu. Pindahkan atau hapus kartu terlebih dahulu.',
                ], 422);
            }

            return back()->with('error', 'Kolom tidak bisa dihapus karena masih memiliki kartu. Pindahkan atau hapus kartu terlebih dahulu.');
        }

        $column->delete();

        // Reorder remaining columns
        Column::where('board_id', $board->id)
            ->where('order', '>', $deletedOrder)
            ->decrement('order');

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Kolom berhasil dihapus!',
            ]);
        }

        return back()->with('success', 'Kolom berhasil dihapus!');
    }

    /**
     * Force delete column with all its cards
     */
    public function forceDestroy(Request $request, Column $column)
    {
        $board = $column->board;
        $deletedOrder = $column->order;

        // Delete all cards in this column (will cascade to attachments & assignments)
        $column->cards()->delete();
        $column->delete();

        // Reorder remaining columns
        Column::where('board_id', $board->id)
            ->where('order', '>', $deletedOrder)
            ->decrement('order');

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Kolom dan semua kartunya berhasil dihapus!',
            ]);
        }

        return back()->with('success', 'Kolom dan semua kartunya berhasil dihapus!');
    }
}

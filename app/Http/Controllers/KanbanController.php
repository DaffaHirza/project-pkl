<?php

namespace App\Http\Controllers;

use App\Models\Board;
use App\Models\Column;
use Illuminate\Http\Request;

class KanbanController extends Controller
{
    /**
     * Display all boards for the authenticated user
     */
    public function index(Request $request)
    {
        $userId = $request->user()->id;
        $boards = Board::where('created_by', $userId)->get();
        return view('kanban.index', compact('boards'));
    }

    /**
     * Display a specific board with its columns and cards
     */
    public function show(Board $board)
    {
        // Load columns with cards and their assigned users
        $board->load(['columns' => function ($query) {
            $query->with(['cards' => function ($query) {
                $query->with('assignedUsers');
            }])->orderBy('order');
        }]);

        return view('kanban.show', compact('board'));
    }

    /**
     * Store a newly created board
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $board = Board::create([
            ...$validated,
            'created_by' => $request->user()->id,
        ]);

        // Create default columns
        $defaultColumns = ['To Do', 'In Progress', 'Done'];
        foreach ($defaultColumns as $index => $columnName) {
            Column::create([
                'board_id' => $board->id,
                'name' => $columnName,
                'order' => $index,
            ]);
        }

        return redirect()->route('kanban.show', $board)->with('success', 'Board created successfully!');
    }

    /**
     * Update the specified board
     */
    public function update(Request $request, Board $board)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $board->update($validated);

        return redirect()->route('kanban.show', $board)->with('success', 'Board updated successfully!');
    }

    /**
     * Delete the specified board
     */
    public function destroy(Board $board)
    {
        $board->delete();
        return redirect()->route('kanban.index')->with('success', 'Board deleted successfully!');
    }
}

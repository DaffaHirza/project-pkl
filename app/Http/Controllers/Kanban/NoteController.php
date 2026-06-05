<?php

namespace App\Http\Controllers\Kanban;

use App\Http\Controllers\Controller;
use App\Models\AssetKanban;
use App\Models\AssetNoteKanban;
use App\Services\KanbanNotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NoteController extends Controller
{
    // Add note to an asset
    public function store(Request $request, AssetKanban $asset)
    {
        $validated = $request->validate([
            'stage' => 'required|integer|min:1|max:13',
            'type' => 'required|in:note,rejection,blocked',
            'content' => 'required|string|max:1000',
        ], [
            'type.in' => 'Tipe catatan tidak valid.',
            'content.required' => 'Isi catatan wajib diisi.',
        ]);

        // Sanitize content (remove dangerous HTML but keep basic formatting)
        $content = trim(strip_tags($validated['content']));

        $note = $asset->notes()->create([
            'user_id' => Auth::id(),
            'stage' => $validated['stage'],
            'type' => $validated['type'],
            'content' => strip_tags(trim($validated['content'])),
        ]);

        // Notify all users when a new note is added
        KanbanNotificationService::notifyNoteAdded($asset, $note, Auth::user());

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Catatan berhasil ditambahkan.',
                'note' => $note->load('user'),
            ]);
        }

        return back()->with('success', 'Catatan berhasil ditambahkan.');
    }

    // Delete note
    public function destroy(AssetNoteKanban $note)
    {
        // Only allow delete own notes or stage_change notes
        if ($note->user_id !== Auth::id() && $note->type !== 'stage_change') {
            return response()->json(['success' => false, 'message' => 'Tidak bisa menghapus catatan orang lain.'], 403);
        }

        $note->delete();

        if (request()->wantsJson()) {
            return response()->json(['success' => true, 'message' => 'Catatan berhasil dihapus.']);
        }

        return back()->with('success', 'Catatan berhasil dihapus.');
    }
}

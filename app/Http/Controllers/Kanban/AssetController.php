<?php

namespace App\Http\Controllers\Kanban;

use App\Http\Controllers\Controller;
use App\Models\ClientKanban;
use App\Models\AssetKanban;
use App\Services\KanbanNotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AssetController extends Controller
{
    /**
     * Display kanban board with drag-and-drop
     */
    public function index(Request $request)
    {
        $query = AssetKanban::query()
            ->select('id', 'client_id', 'name', 'asset_type', 'current_stage', 'position', 'updated_at')
            ->with(['client:id,name,company_name']);

        // Filter by client
        if ($request->filled('client_id')) {
            $query->where('client_id', (int) $request->client_id);
        }

        $assets = $query->orderBy('position')->orderBy('updated_at', 'desc')->get();

        // Group assets by stage
        $assetsByStage = [];
        foreach (AssetKanban::STAGES as $stageNum => $stageName) {
            $assetsByStage[$stageNum] = $assets->where('current_stage', $stageNum)->values();
        }

        $stages = AssetKanban::STAGES;
        $clients = ClientKanban::select('id', 'name', 'company_name')->orderBy('name')->get();

        return view('kanban.assets.index', compact('assetsByStage', 'stages', 'clients'));
    }

    /**
     * Show create form with clients dropdown
     */
    public function create(Request $request)
    {
        $clients = ClientKanban::query()
            ->select('id', 'name', 'company_name', 'type', 'parent_id')
            ->orderBy('name')
            ->get();
        
        $selectedClientId = $request->get('client_id');
        $assetTypes = AssetKanban::ASSET_TYPES;

        return view('kanban.assets.create', compact('clients', 'selectedClientId', 'assetTypes'));
    }

    /**
     * Store new asset with validation, logging & notification
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'client_id' => 'required|exists:clients_kanban,id',
            'name' => 'required|string|max:255|min:3',
            'asset_type' => 'required|string|in:' . implode(',', array_keys(AssetKanban::ASSET_TYPES)),
            'location' => 'nullable|string|max:500',
        ], [
            'name.required' => 'Nama asset wajib diisi.',
            'name.min' => 'Nama minimal 3 karakter.',
            'asset_type.in' => 'Tipe asset tidak valid.',
        ]);

        // Sanitize
        $validated['name'] = strip_tags(trim($validated['name']));
        $validated['location'] = $validated['location'] ? strip_tags($validated['location']) : null;

        $asset = AssetKanban::create($validated);

        // Log initial creation as activity
        $asset->notes()->create([
            'user_id' => Auth::id(),
            'stage' => 1,
            'type' => 'stage_change',
            'content' => 'Asset dibuat dan memulai tahap Inisiasi',
        ]);

        // Notify admins
        KanbanNotificationService::notifyAssetCreated($asset, Auth::user());

        return redirect()
            ->route('kanban.assets.show', $asset)
            ->with('success', 'Asset berhasil ditambahkan.');
    }

    /**
     * Display asset detail with documents & notes grouped by stage
     */
    public function show(AssetKanban $asset)
    {
        $asset->load([
            'client:id,name,company_name,type,parent_id',
            'client.parent:id,name,company_name',
            'documents' => fn($q) => $q->select('id', 'asset_id', 'uploaded_by', 'stage', 'file_name', 'file_path', 'file_type', 'file_size', 'created_at')
                                       ->with('uploader:id,name')
                                       ->orderBy('created_at', 'desc'),
            'notes' => fn($q) => $q->select('id', 'asset_id', 'user_id', 'stage', 'type', 'content', 'created_at')
                                   ->with('user:id,name')
                                   ->orderBy('created_at', 'desc')
        ]);
        
        $stages = AssetKanban::STAGES;
        
        // Group by stage efficiently (data already loaded)
        $documentsByStage = collect($stages)->mapWithKeys(fn($label, $num) => [
            $num => $asset->documents->where('stage', $num)->values()
        ]);
        
        $notesByStage = collect($stages)->mapWithKeys(fn($label, $num) => [
            $num => $asset->notes->where('stage', $num)->values()
        ]);

        return view('kanban.assets.show', compact('asset', 'stages', 'documentsByStage', 'notesByStage'));
    }

    /**
     * Show edit form
     */
    public function edit(AssetKanban $asset)
    {
        $clients = ClientKanban::query()
            ->select('id', 'name', 'company_name', 'type', 'parent_id')
            ->orderBy('name')
            ->get();
        
        $assetTypes = AssetKanban::ASSET_TYPES;

        return view('kanban.assets.edit', compact('asset', 'clients', 'assetTypes'));
    }

    /**
     * Update asset with validation
     */
    public function update(Request $request, AssetKanban $asset)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|min:3',
            'asset_type' => 'required|string|in:' . implode(',', array_keys(AssetKanban::ASSET_TYPES)),
            'location' => 'nullable|string|max:500',
        ]);

        $validated['name'] = strip_tags(trim($validated['name']));
        $validated['location'] = $validated['location'] ? strip_tags($validated['location']) : null;

        $asset->update($validated);

        return redirect()
            ->route('kanban.assets.show', $asset)
            ->with('success', 'Asset berhasil diupdate.');
    }

    /**
     * Soft delete asset
     */
    public function destroy(AssetKanban $asset)
    {
        $clientId = $asset->client_id;
        $assetName = $asset->name;
        
        $asset->delete();

        return redirect()
            ->route('kanban.clients.show', $clientId)
            ->with('success', "Asset '{$assetName}' berhasil dihapus.");
    }

    // ==========================================
    // KANBAN OPERATIONS (API)
    // ==========================================

    /**
     * Move asset to specific stage with notification & logging
     */
    public function moveStage(Request $request, AssetKanban $asset)
    {
        $validated = $request->validate([
            'stage' => 'nullable|integer|min:1|max:13',
            'direction' => 'nullable|in:next,prev',
            'note' => 'nullable|string|max:500',
        ]);

        $oldStage = $asset->current_stage;
        
        // Determine new stage from direction or direct stage
        if (isset($validated['direction'])) {
            $newStage = $validated['direction'] === 'next' 
                ? min($oldStage + 1, 13) 
                : max($oldStage - 1, 1);
        } elseif (isset($validated['stage'])) {
            $newStage = (int) $validated['stage'];
        } else {
            if ($request->wantsJson()) {
                return response()->json(['success' => false, 'message' => 'Stage atau direction diperlukan'], 400);
            }
            return back()->with('error', 'Stage atau direction diperlukan');
        }

        if ($oldStage === $newStage) {
            if ($request->wantsJson()) {
                return response()->json(['success' => true, 'message' => 'Tidak ada perubahan']);
            }
            return back()->with('info', 'Tidak ada perubahan stage');
        }

        // Use transaction for data integrity
        DB::beginTransaction();
        try {
            $note = $validated['note'] ?? null;
            $success = $asset->moveToStage($newStage, Auth::id(), $note);

            if ($success) {
                // Send notification to admins
                KanbanNotificationService::notifyStageChange(
                    $asset, 
                    $oldStage, 
                    $newStage, 
                    Auth::user(), 
                    $note
                );

                DB::commit();

                if ($request->wantsJson()) {
                    return response()->json([
                        'success' => true,
                        'message' => 'Asset dipindahkan ke ' . AssetKanban::STAGES[$newStage],
                        'asset' => $asset->fresh(['client:id,name']),
                        'old_stage' => $oldStage,
                        'new_stage' => $newStage,
                    ]);
                }
                
                return back()->with('success', 'Asset dipindahkan ke ' . AssetKanban::STAGES[$newStage]);
            }

            DB::rollBack();
            if ($request->wantsJson()) {
                return response()->json(['success' => false, 'message' => 'Gagal memindahkan asset'], 400);
            }
            return back()->with('error', 'Gagal memindahkan asset');

        } catch (\Exception $e) {
            DB::rollBack();
            if ($request->wantsJson()) {
                return response()->json(['success' => false, 'message' => 'Terjadi kesalahan sistem'], 500);
            }
            return back()->with('error', 'Terjadi kesalahan sistem');
        }
    }

    /**
     * Update position within stage (drag & drop)
     */
    public function updatePosition(Request $request, AssetKanban $asset)
    {
        $validated = $request->validate([
            'position' => 'required|integer|min:0|max:9999',
        ]);

        $asset->update(['position' => $validated['position']]);

        return response()->json(['success' => true]);
    }
}

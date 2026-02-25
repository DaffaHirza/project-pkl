<?php

namespace App\Http\Controllers\Kanban;

use App\Http\Controllers\Controller;
use App\Models\ProjectKanban;
use App\Models\ProjectAssetKanban;
use App\Models\User;
use App\Models\Notification;
use App\Services\KanbanNotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AssetController extends Controller
{
    /**
     * Display paginated assets with filters
     */
    public function index(Request $request)
    {
        $query = ProjectAssetKanban::query()
            ->select('project_assets_kanban.id', 'project_assets_kanban.project_id', 
                     'project_assets_kanban.name', 'project_assets_kanban.asset_code',
                     'project_assets_kanban.asset_type', 'project_assets_kanban.current_stage',
                     'project_assets_kanban.priority', 'project_assets_kanban.created_at')
            ->with(['project:id,name,project_code,client_id', 'project.client:id,name']);

        // Filter by project
        if ($request->filled('project_id')) {
            $query->where('project_id', (int) $request->project_id);
        }

        // Filter by stage
        if ($request->filled('stage')) {
            $stage = (int) $request->stage;
            if ($stage >= 1 && $stage <= 13) {
                $query->where('current_stage', $stage);
            }
        }

        // Filter by priority
        if ($request->filled('priority') && in_array($request->priority, ['normal', 'warning', 'critical'])) {
            $query->where('priority', $request->priority);
        }

        // Search
        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->where(function ($q) use ($search) {
                $q->where('project_assets_kanban.name', 'like', "%{$search}%")
                  ->orWhere('asset_code', 'like', "%{$search}%");
            });
        }

        $assets = $query->latest('project_assets_kanban.created_at')->paginate(15)->withQueryString();
        $stages = ProjectAssetKanban::STAGES;
        $priorities = ProjectAssetKanban::PRIORITIES;

        return view('kanban.assets.index', compact('assets', 'stages', 'priorities'));
    }

    /**
     * Kanban board view with drag-and-drop
     */
    public function board(Request $request)
    {
        $query = ProjectAssetKanban::query()
            ->select('id', 'project_id', 'name', 'asset_code', 'asset_type', 
                     'current_stage', 'priority', 'position', 'updated_at')
            ->with(['project:id,name,project_code']);

        // Filter by project
        if ($request->filled('project_id')) {
            $query->where('project_id', (int) $request->project_id);
        }

        $assets = $query->orderBy('position')->orderBy('updated_at', 'desc')->get();

        // Group assets by stage
        $assetsByStage = [];
        foreach (ProjectAssetKanban::STAGES as $stageNum => $stageName) {
            $assetsByStage[$stageNum] = $assets->where('current_stage', $stageNum)->values();
        }

        $stages = ProjectAssetKanban::STAGES;
        $projects = ProjectKanban::select('id', 'name', 'project_code')->where('status', 'active')->get();

        return view('kanban.assets.board', compact('assetsByStage', 'stages', 'projects'));
    }

    /**
     * Show create form with active projects
     */
    public function create(Request $request)
    {
        $projects = ProjectKanban::query()
            ->select('projects_kanban.id', 'projects_kanban.name', 'projects_kanban.project_code', 'projects_kanban.client_id')
            ->with('client:id,name,company_name')
            ->where('status', 'active')
            ->orderBy('name')
            ->get();
        
        $selectedProjectId = $request->get('project_id');
        $assetTypes = ProjectAssetKanban::ASSET_TYPES;

        return view('kanban.assets.create', compact('projects', 'selectedProjectId', 'assetTypes'));
    }

    /**
     * Store new asset with validation, logging & notification
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'project_id' => 'required|exists:projects_kanban,id',
            'name' => 'required|string|max:255|min:3',
            'description' => 'nullable|string|max:2000',
            'asset_type' => 'required|string|in:' . implode(',', array_keys(ProjectAssetKanban::ASSET_TYPES)),
            'location' => 'nullable|string|max:500',
        ], [
            'name.required' => 'Nama asset wajib diisi.',
            'name.min' => 'Nama minimal 3 karakter.',
            'asset_type.in' => 'Tipe asset tidak valid.',
        ]);

        // Sanitize
        $validated['name'] = strip_tags(trim($validated['name']));
        $validated['description'] = $validated['description'] ? strip_tags($validated['description']) : null;
        $validated['location'] = $validated['location'] ? strip_tags($validated['location']) : null;

        $asset = ProjectAssetKanban::create($validated);

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
    public function show(ProjectAssetKanban $asset)
    {
        $asset->load([
            'project:id,name,project_code,client_id',
            'project.client:id,name,company_name',
            'documents' => fn($q) => $q->select('id', 'asset_id', 'uploaded_by', 'stage', 'file_name', 'file_path', 'file_type', 'file_size', 'created_at')
                                       ->with('uploader:id,name')
                                       ->orderBy('created_at', 'desc'),
            'notes' => fn($q) => $q->select('id', 'asset_id', 'user_id', 'stage', 'type', 'content', 'created_at')
                                   ->with('user:id,name')
                                   ->orderBy('created_at', 'desc')
        ]);
        
        $stages = ProjectAssetKanban::STAGES;
        
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
    public function edit(ProjectAssetKanban $asset)
    {
        $projects = ProjectKanban::query()
            ->select('id', 'name', 'project_code', 'client_id')
            ->with('client:id,name,company_name')
            ->where('status', 'active')
            ->orderBy('name')
            ->get();
        
        $assetTypes = ProjectAssetKanban::ASSET_TYPES;
        $priorities = ProjectAssetKanban::PRIORITIES;

        return view('kanban.assets.edit', compact('asset', 'projects', 'assetTypes', 'priorities'));
    }

    /**
     * Update asset with validation
     */
    public function update(Request $request, ProjectAssetKanban $asset)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|min:3',
            'description' => 'nullable|string|max:2000',
            'asset_type' => 'required|string|in:' . implode(',', array_keys(ProjectAssetKanban::ASSET_TYPES)),
            'location' => 'nullable|string|max:500',
            'priority' => 'required|in:normal,warning,critical',
        ]);

        $validated['name'] = strip_tags(trim($validated['name']));
        $validated['description'] = $validated['description'] ? strip_tags($validated['description']) : null;
        $validated['location'] = $validated['location'] ? strip_tags($validated['location']) : null;

        $asset->update($validated);

        return redirect()
            ->route('kanban.assets.show', $asset)
            ->with('success', 'Asset berhasil diupdate.');
    }

    /**
     * Soft delete asset
     */
    public function destroy(ProjectAssetKanban $asset)
    {
        $projectId = $asset->project_id;
        $assetName = $asset->name;
        
        $asset->delete();

        return redirect()
            ->route('kanban.projects.show', $projectId)
            ->with('success', "Asset '{$assetName}' berhasil dihapus.");
    }

    // ==========================================
    // KANBAN OPERATIONS (API)
    // ==========================================

    /**
     * Move asset to specific stage with notification & logging
     */
    public function moveStage(Request $request, ProjectAssetKanban $asset)
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
                        'message' => 'Asset dipindahkan ke ' . ProjectAssetKanban::STAGES[$newStage],
                        'asset' => $asset->fresh(['project:id,name']),
                        'old_stage' => $oldStage,
                        'new_stage' => $newStage,
                    ]);
                }
                
                return back()->with('success', 'Asset dipindahkan ke ' . ProjectAssetKanban::STAGES[$newStage]);
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
    public function updatePosition(Request $request, ProjectAssetKanban $asset)
    {
        $validated = $request->validate([
            'position' => 'required|integer|min:0|max:9999',
        ]);

        $asset->update(['position' => $validated['position']]);

        return response()->json(['success' => true]);
    }

    /**
     * Update priority with notification for critical
     */
    public function updatePriority(Request $request, ProjectAssetKanban $asset)
    {
        $validated = $request->validate([
            'priority' => 'required|in:normal,warning,critical',
        ]);

        $oldPriority = $asset->priority;
        $newPriority = $validated['priority'];

        $asset->update(['priority' => $newPriority]);

        // Notify if changed to critical
        if ($newPriority === 'critical' && $oldPriority !== 'critical') {
            KanbanNotificationService::notifyPriorityCritical($asset, Auth::user());
        }

        return response()->json([
            'success' => true,
            'message' => 'Priority diubah ke ' . ProjectAssetKanban::PRIORITIES[$newPriority],
        ]);
    }

    /**
     * Bulk create multiple assets efficiently
     */
    public function bulkStore(Request $request)
    {
        $validated = $request->validate([
            'project_id' => 'required|exists:projects_kanban,id',
            'assets' => 'required|array|min:1|max:50',
            'assets.*.name' => 'required|string|max:255|min:3',
            'assets.*.asset_type' => 'required|string|in:' . implode(',', array_keys(ProjectAssetKanban::ASSET_TYPES)),
            'assets.*.location' => 'nullable|string|max:500',
        ], [
            'assets.max' => 'Maksimal 50 asset per batch.',
        ]);

        $created = [];
        
        DB::beginTransaction();
        try {
            foreach ($validated['assets'] as $assetData) {
                $asset = ProjectAssetKanban::create([
                    'project_id' => $validated['project_id'],
                    'name' => strip_tags(trim($assetData['name'])),
                    'asset_type' => $assetData['asset_type'],
                    'location' => isset($assetData['location']) ? strip_tags($assetData['location']) : null,
                ]);

                $asset->notes()->create([
                    'user_id' => Auth::id(),
                    'stage' => 1,
                    'type' => 'stage_change',
                    'content' => 'Asset dibuat dan memulai tahap Inisiasi',
                ]);

                $created[] = $asset;
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => count($created) . ' asset berhasil ditambahkan',
                'count' => count($created),
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Gagal menambahkan asset'], 500);
        }
    }
}

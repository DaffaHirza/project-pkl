<?php

namespace App\Http\Controllers\Appraisal;

use App\Http\Controllers\Controller;
use App\Models\ProjectKanban;
use App\Models\ProjectAsset;
use App\Models\ActivityKanban;
use Illuminate\Http\Request;

class ProjectAssetController extends Controller
{
    /**
     * Display kanban board of assets (optionally filtered by project)
     * This is the main kanban view where assets are shown as cards
     */
    public function index(Request $request)
    {
        $query = ProjectAsset::with(['project.client', 'latestInspection', 'latestWorkingPaper']);

        // Filter by project
        if ($request->filled('project_id')) {
            $query->where('project_id', $request->project_id);
        }

        // Filter by priority status
        if ($request->filled('priority')) {
            $query->where('priority_status', $request->priority);
        }

        // Filter by asset type
        if ($request->filled('asset_type')) {
            $query->where('asset_type', $request->asset_type);
        }

        // Search by asset name or code
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('asset_code', 'like', "%{$search}%")
                  ->orWhere('location_address', 'like', "%{$search}%");
            });
        }

        $assets = $query->orderBy('created_at', 'desc')->get();

        // Group assets by stage for kanban view
        $stages = ProjectAsset::STAGES;
        $assetsByStage = [];
        foreach ($stages as $stageKey => $stageLabel) {
            $assetsByStage[$stageKey] = $assets->where('current_stage', $stageKey)->values();
        }

        $projects = ProjectKanban::with('client')
            ->ongoing()
            ->orderBy('name')
            ->get();

        $assetTypes = ProjectAsset::ASSET_TYPES;

        return view('appraisal.assets.index', compact('assetsByStage', 'stages', 'projects', 'assetTypes'));
    }

    /**
     * Display list view of all assets
     */
    public function list(Request $request)
    {
        $query = ProjectAsset::with(['project.client']);

        // Filters
        if ($request->filled('project_id')) {
            $query->where('project_id', $request->project_id);
        }

        if ($request->filled('stage')) {
            $query->where('current_stage', $request->stage);
        }

        if ($request->filled('priority')) {
            $query->where('priority_status', $request->priority);
        }

        if ($request->filled('asset_type')) {
            $query->where('asset_type', $request->asset_type);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('asset_code', 'like', "%{$search}%");
            });
        }

        $assets = $query->orderBy('created_at', 'desc')->paginate(20);
        $stages = ProjectAsset::STAGES;
        $assetTypes = ProjectAsset::ASSET_TYPES;
        $projects = ProjectKanban::with('client')->ongoing()->orderBy('name')->get();

        return view('appraisal.assets.list', compact('assets', 'stages', 'assetTypes', 'projects'));
    }

    /**
     * Show the form for creating a new asset
     */
    public function create(Request $request, ?ProjectKanban $project = null)
    {
        // If coming from project context
        if ($request->filled('project_id')) {
            $project = ProjectKanban::findOrFail($request->project_id);
        }

        $projects = ProjectKanban::with('client')
            ->ongoing()
            ->orderBy('name')
            ->get();
        
        $assetTypes = ProjectAsset::ASSET_TYPES;

        return view('appraisal.assets.create', compact('projects', 'assetTypes', 'project'));
    }

    /**
     * Store a newly created asset
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'project_id' => 'required|exists:projects_kanban,id',
            'name' => 'required|string|max:255',
            'asset_type' => 'required|in:' . implode(',', array_keys(ProjectAsset::ASSET_TYPES)),
            'location_address' => 'required|string|max:500',
            'description' => 'nullable|string|max:2000',
            'target_completion_date' => 'nullable|date',
            'notes' => 'nullable|string|max:2000',
        ]);

        $validated['asset_code'] = ProjectAsset::generateAssetCode($validated['project_id']);
        $validated['current_stage'] = 'pending';
        $validated['priority_status'] = 'normal';

        $asset = ProjectAsset::create($validated);

        // Update parent project's asset count
        $asset->project->updateProgress();

        // Log activity
        ActivityKanban::logAssetCreated($asset, $request->user());

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Objek berhasil ditambahkan',
                'asset' => $asset->load('project.client'),
            ]);
        }

        return redirect()
            ->route('appraisal.assets.show', $asset)
            ->with('success', 'Objek berhasil ditambahkan dengan kode: ' . $asset->asset_code);
    }

    /**
     * Display the specified asset with all related data
     */
    public function show(ProjectAsset $asset)
    {
        $asset->load([
            'project.client',
            'project.proposals' => fn($q) => $q->orderBy('created_at', 'desc'),
            'project.contracts' => fn($q) => $q->orderBy('created_at', 'desc'),
            'inspections' => fn($q) => $q->with('surveyor')->orderBy('inspection_date', 'desc'),
            'workingPapers' => fn($q) => $q->with('analyst'),
            'reports' => fn($q) => $q->orderBy('created_at', 'desc'),
            'approvals' => fn($q) => $q->with('user')->orderBy('created_at', 'desc'),
            'documents' => fn($q) => $q->with('uploader')->orderBy('created_at', 'desc'),
            'activities' => fn($q) => $q->with('user')->orderBy('created_at', 'desc')->limit(20),
        ]);

        $stages = ProjectAsset::STAGES;

        return view('appraisal.assets.show', compact('asset', 'stages'));
    }

    /**
     * Show the form for editing the specified asset
     */
    public function edit(ProjectAsset $asset)
    {
        $projects = ProjectKanban::with('client')
            ->ongoing()
            ->orderBy('name')
            ->get();
        
        $stages = ProjectAsset::STAGES;
        $assetTypes = ProjectAsset::ASSET_TYPES;
        
        return view('appraisal.assets.edit', compact('asset', 'projects', 'stages', 'assetTypes'));
    }

    /**
     * Update the specified asset
     */
    public function update(Request $request, ProjectAsset $asset)
    {
        $validated = $request->validate([
            'project_id' => 'sometimes|exists:projects_kanban,id',
            'name' => 'required|string|max:255',
            'asset_type' => 'required|in:' . implode(',', array_keys(ProjectAsset::ASSET_TYPES)),
            'location_address' => 'required|string|max:500',
            'description' => 'nullable|string|max:2000',
            'target_completion_date' => 'nullable|date',
            'current_stage' => 'sometimes|in:' . implode(',', array_keys(ProjectAsset::STAGES)),
            'priority_status' => 'sometimes|in:normal,warning,critical',
            'notes' => 'nullable|string|max:2000',
        ]);

        $asset->update($validated);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Objek berhasil diperbarui',
                'asset' => $asset->fresh(['project.client']),
            ]);
        }

        return redirect()
            ->route('appraisal.assets.show', $asset)
            ->with('success', 'Objek berhasil diperbarui.');
    }

    /**
     * Move asset to different stage (AJAX - Drag & Drop)
     */
    public function moveStage(Request $request, ProjectAsset $asset)
    {
        $validated = $request->validate([
            'stage' => 'required|in:' . implode(',', array_keys(ProjectAsset::STAGES)),
        ]);

        $oldStage = $asset->current_stage;
        $newStage = $validated['stage'];

        if ($oldStage !== $newStage) {
            $asset->update(['current_stage' => $newStage]);

            // Log activity (only if user is authenticated)
            if ($request->user()) {
                ActivityKanban::logAssetStageMove($asset, $request->user(), $oldStage, $newStage);
            }

            // If moving to 'done', log completion and update parent project
            if ($newStage === 'done') {
                if ($request->user()) {
                    ActivityKanban::logAssetCompleted($asset, $request->user());
                }
                $asset->project->updateProgress();
            }

            if ($request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => "Objek dipindahkan ke tahap: " . ProjectAsset::STAGES[$newStage],
                    'asset' => $asset->fresh(['project.client']),
                ]);
            }
        }

        return redirect()->back()->with('success', 'Tahap objek berhasil diubah.');
    }

    /**
     * Update asset priority status
     */
    public function updatePriority(Request $request, ProjectAsset $asset)
    {
        $validated = $request->validate([
            'priority_status' => 'required|in:normal,warning,critical',
        ]);

        $asset->update(['priority_status' => $validated['priority_status']]);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Status prioritas berhasil diubah',
                'asset' => $asset->fresh(),
            ]);
        }

        return redirect()->back()->with('success', 'Status prioritas berhasil diubah.');
    }

    /**
     * Soft delete asset
     */
    public function destroy(Request $request, ProjectAsset $asset)
    {
        $project = $asset->project;
        $asset->delete();
        
        // Update parent project's asset count
        $project->updateProgress();

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Objek berhasil dihapus',
            ]);
        }

        return redirect()
            ->route('appraisal.projects.show', $project)
            ->with('success', 'Objek berhasil dihapus.');
    }

    /**
     * Restore soft deleted asset
     */
    public function restore(Request $request, int $id)
    {
        $asset = ProjectAsset::withTrashed()->findOrFail($id);
        $asset->restore();
        
        // Update parent project's asset count
        $asset->project->updateProgress();

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Objek berhasil dipulihkan',
                'asset' => $asset->fresh(['project.client']),
            ]);
        }

        return redirect()
            ->route('appraisal.assets.show', $asset)
            ->with('success', 'Objek berhasil dipulihkan.');
    }

    /**
     * Get asset statistics
     */
    public function statistics(Request $request)
    {
        $query = ProjectAsset::query();

        if ($request->filled('project_id')) {
            $query->where('project_id', $request->project_id);
        }

        $assets = $query->get();

        $statistics = [
            'total' => $assets->count(),
            'by_stage' => [],
            'by_priority' => [],
            'by_asset_type' => [],
            'overdue' => $assets->filter(fn($a) => $a->isOverdue())->count(),
            'completed' => $assets->where('current_stage', 'done')->count(),
        ];

        foreach (ProjectAsset::STAGES as $key => $label) {
            $statistics['by_stage'][$key] = [
                'label' => $label,
                'count' => $assets->where('current_stage', $key)->count(),
            ];
        }

        foreach (ProjectAsset::PRIORITY_STATUS as $key => $label) {
            $statistics['by_priority'][$key] = [
                'label' => $label,
                'count' => $assets->where('priority_status', $key)->count(),
            ];
        }

        foreach (ProjectAsset::ASSET_TYPES as $key => $label) {
            $count = $assets->where('asset_type', $key)->count();
            if ($count > 0) {
                $statistics['by_asset_type'][$key] = [
                    'label' => $label,
                    'count' => $count,
                ];
            }
        }

        return response()->json($statistics);
    }

    /**
     * Bulk create assets for a project
     */
    public function bulkStore(Request $request)
    {
        $validated = $request->validate([
            'project_id' => 'required|exists:projects_kanban,id',
            'assets' => 'required|array|min:1',
            'assets.*.name' => 'required|string|max:255',
            'assets.*.asset_type' => 'required|in:' . implode(',', array_keys(ProjectAsset::ASSET_TYPES)),
            'assets.*.location' => 'required|string|max:500',
            'assets.*.description' => 'nullable|string|max:2000',
        ]);

        $project = ProjectKanban::findOrFail($validated['project_id']);
        $createdAssets = [];

        foreach ($validated['assets'] as $assetData) {
            $assetData['project_id'] = $project->id;
            $assetData['asset_code'] = ProjectAsset::generateAssetCode($project->id);
            $assetData['current_stage'] = 'pending';
            $assetData['priority_status'] = 'normal';

            $asset = ProjectAsset::create($assetData);
            ActivityKanban::logAssetCreated($asset, $request->user());
            $createdAssets[] = $asset;
        }

        // Update parent project's asset count
        $project->updateProgress();

        return response()->json([
            'success' => true,
            'message' => count($createdAssets) . ' objek berhasil ditambahkan',
            'assets' => $createdAssets,
        ]);
    }
}

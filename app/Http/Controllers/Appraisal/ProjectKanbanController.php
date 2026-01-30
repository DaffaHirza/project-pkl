<?php

namespace App\Http\Controllers\Appraisal;

use App\Http\Controllers\Controller;
use App\Models\ProjectKanban;
use App\Models\ProjectAsset;
use App\Models\KanbanClient;
use App\Models\ActivityKanban;
use Illuminate\Http\Request;
use App\Models\User;

class ProjectKanbanController extends Controller
{
    /**
     * Display kanban board view with all projects grouped by ADMIN stage
     * NOTE: For technical workflow (per asset), use ProjectAssetController@index
     */
    public function index(Request $request)
    {
        $query = ProjectKanban::with(['client', 'latestProposal', 'latestContract', 'assets']);

        // Filter by priority status
        if ($request->filled('priority')) {
            $query->where('priority_status', $request->priority);
        }

        // Filter by client
        if ($request->filled('client_id')) {
            $query->where('client_id', $request->client_id);
        }

        // Search by project name or code
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('project_code', 'like', "%{$search}%")
                  ->orWhere('location', 'like', "%{$search}%");
            });
        }

        $projects = $query->orderBy('created_at', 'desc')->get();

        // Group projects by stage for kanban view
        $stages = ProjectKanban::STAGES;
        $projectsByStage = [];
        foreach ($stages as $stageKey => $stageLabel) {
            $projectsByStage[$stageKey] = $projects->where('current_stage', $stageKey)->values();
        }

        $clients = KanbanClient::orderBy('name')->get();

        return view('appraisal.projects.index', compact('projectsByStage', 'stages', 'clients'));
    }

    /**
     * Display list view of all projects
     */
    public function list(Request $request)
    {
        $query = ProjectKanban::with(['client', 'assets']);

        // Filters
        if ($request->filled('stage')) {
            $query->where('current_stage', $request->stage);
        }

        if ($request->filled('priority')) {
            $query->where('priority_status', $request->priority);
        }

        if ($request->filled('client_id')) {
            $query->where('client_id', $request->client_id);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('project_code', 'like', "%{$search}%");
            });
        }

        $projects = $query->orderBy('created_at', 'desc')->paginate(20);
        $stages = ProjectKanban::STAGES;
        $clients = KanbanClient::orderBy('name')->get();

        return view('appraisal.projects.list', compact('projects', 'stages', 'clients'));
    }

    /**
     * Show the form for creating a new project
     */
    public function create()
    {
        $clients = KanbanClient::orderBy('name')->get();
        return view('appraisal.projects.create', compact('clients'));
    }

    /**
     * Store a newly created project
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'client_id' => 'required|exists:kanban_clients,id',
            'name' => 'required|string|max:255',
            'location' => 'required|string|max:500',
            'due_date' => 'nullable|date|after:today',
        ]);

        $validated['project_code'] = ProjectKanban::generateProjectCode();
        $validated['current_stage'] = 'lead';
        $validated['priority_status'] = 'normal';
        $validated['total_assets'] = 0;

        $project = ProjectKanban::create($validated);

        // Log activity
        ActivityKanban::create([
            'project_id' => $project->id,
            'user_id' => $request->user()->id,
            'activity_type' => 'comment',
            'stage_context' => 'lead',
            'description' => 'Project baru dibuat.',
        ]);

        return redirect()
            ->route('appraisal.projects.show', $project)
            ->with('success', 'Project berhasil dibuat dengan kode: ' . $project->project_code);
    }

    /**
     * Display the specified project with all related data
     * Now includes assets and their progress
     */
    public function show(ProjectKanban $project)
    {
        $project->load([
            'client',
            'assets' => fn($q) => $q->orderBy('created_at', 'desc'),
            'proposals' => fn($q) => $q->orderBy('created_at', 'desc'),
            'contracts' => fn($q) => $q->orderBy('created_at', 'desc'),
            'invoices' => fn($q) => $q->orderBy('created_at', 'desc'),
            'documents' => fn($q) => $q->with('uploader')->projectLevel()->orderBy('created_at', 'desc'),
            'activities' => fn($q) => $q->with(['user', 'asset'])->orderBy('created_at', 'desc')->limit(30),
        ]);

        $stages = ProjectKanban::STAGES;
        $assetStages = ProjectAsset::STAGES;
        $assetTypes = ProjectAsset::ASSET_TYPES;
        
        // Get assets grouped by stage for mini kanban view
        $assetsByStage = [];
        foreach ($assetStages as $stageKey => $stageLabel) {
            $assetsByStage[$stageKey] = $project->assets->where('current_stage', $stageKey)->values();
        }

        return view('appraisal.projects.show', compact(
            'project', 
            'stages', 
            'assetStages',
            'assetTypes',
            'assetsByStage'
        ));
    }

    /**
     * Show the form for editing the specified project
     */
    public function edit(ProjectKanban $project)
    {
        $clients = KanbanClient::orderBy('name')->get();
        $stages = ProjectKanban::STAGES;
        
        return view('appraisal.projects.edit', compact('project', 'clients', 'stages'));
    }

    /**
     * Update the specified project
     */
    public function update(Request $request, ProjectKanban $project)
    {
        $validated = $request->validate([
            'client_id' => 'required|exists:kanban_clients,id',
            'name' => 'required|string|max:255',
            'location' => 'required|string|max:500',
            'due_date' => 'nullable|date',
            'priority_status' => 'required|in:normal,warning,critical',
        ]);

        $project->update($validated);

        return redirect()
            ->route('appraisal.projects.show', $project)
            ->with('success', 'Project berhasil diperbarui.');
    }

    /**
     * Move project to different stage (AJAX)
     */
    public function moveStage(Request $request, ProjectKanban $project)
    {
        $validated = $request->validate([
            'stage' => 'required|in:' . implode(',', array_keys(ProjectKanban::STAGES)),
        ]);

        $oldStage = $project->current_stage;
        $newStage = $validated['stage'];

        if ($oldStage !== $newStage) {
            $project->update([
                'current_stage' => $newStage,
            ]);

            // Log activity
            ActivityKanban::logStageMove($project, $request->user(), $oldStage, $newStage);
        }

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Project dipindahkan ke ' . ProjectKanban::STAGES[$newStage],
            ]);
        }

        return back()->with('success', 'Project dipindahkan ke ' . ProjectKanban::STAGES[$newStage]);
    }


    /**
     * Update project priority status
     */
    public function updatePriority(Request $request, ProjectKanban $project)
    {
        $validated = $request->validate([
            'priority_status' => 'required|in:normal,warning,critical',
        ]);

        $project->update($validated);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Status prioritas diperbarui.',
            ]);
        }

        return back()->with('success', 'Status prioritas diperbarui.');
    }

    /**
     * Remove the specified project (soft delete)
     */
    public function destroy(ProjectKanban $project)
    {
        $project->delete();

        return redirect()
            ->route('appraisal.projects.index')
            ->with('success', 'Project berhasil dihapus.');
    }

    /**
     * Restore soft deleted project
     */
    public function restore($id)
    {
        $project = ProjectKanban::withTrashed()->findOrFail($id);
        $project->restore();

        return redirect()
            ->route('appraisal.projects.show', $project)
            ->with('success', 'Project berhasil dipulihkan.');
    }

    /**
     * Get project statistics for dashboard
     */
    public function statistics()
    {
        $stats = [
            'total' => ProjectKanban::count(),
            'by_stage' => [],
            'by_priority' => [
                'normal' => ProjectKanban::where('priority_status', 'normal')->count(),
                'warning' => ProjectKanban::where('priority_status', 'warning')->count(),
                'critical' => ProjectKanban::where('priority_status', 'critical')->count(),
            ],
            'overdue' => ProjectKanban::whereNotNull('due_date')
                ->where('due_date', '<', now())
                ->where('current_stage', '!=', 'done')
                ->count(),
            'total_assets' => ProjectAsset::count(),
            'completed_assets' => ProjectAsset::completed()->count(),
        ];

        foreach (ProjectKanban::STAGES as $key => $label) {
            $stats['by_stage'][$key] = [
                'label' => $label,
                'count' => ProjectKanban::where('current_stage', $key)->count(),
            ];
        }

        return response()->json($stats);
    }
}

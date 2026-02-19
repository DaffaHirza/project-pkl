<?php

namespace App\Http\Controllers\Kanban;

use App\Http\Controllers\Controller;
use App\Models\ClientKanban;
use App\Models\ProjectKanban;
use App\Models\ProjectAssetKanban;
use App\Models\User;
use App\Models\Notification;
use App\Services\KanbanNotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ProjectController extends Controller
{
    /**
     * Display paginated projects with filters
     */
    public function index(Request $request)
    {
        $query = ProjectKanban::query()
            ->select('projects_kanban.id', 'projects_kanban.client_id', 'projects_kanban.name', 
                     'projects_kanban.project_code', 'projects_kanban.status', 
                     'projects_kanban.due_date', 'projects_kanban.created_at')
            ->with('client:id,name,company_name')
            ->withCount('assets');

        // Filter by status
        if ($request->filled('status') && in_array($request->status, ['active', 'completed', 'cancelled'])) {
            $query->where('status', $request->status);
        }

        // Filter by client
        if ($request->filled('client_id')) {
            $query->where('client_id', (int) $request->client_id);
        }

        // Search
        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('project_code', 'like', "%{$search}%");
            });
        }

        $projects = $query->latest()->paginate(15)->withQueryString();
        
        $clients = ClientKanban::select('id', 'name', 'company_name')
            ->orderBy('name')
            ->get();

        return view('kanban.projects.index', compact('projects', 'clients'));
    }

    /**
     * Show create form with clients dropdown
     */
    public function create(Request $request)
    {
        $clients = ClientKanban::select('id', 'name', 'company_name')
            ->orderBy('name')
            ->get();
        $selectedClientId = $request->get('client_id');

        return view('kanban.projects.create', compact('clients', 'selectedClientId'));
    }

    /**
     * Store new project with validation & notification
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'client_id' => 'required|exists:clients_kanban,id',
            'name' => 'required|string|max:255|min:3',
            'description' => 'nullable|string|max:2000',
            'due_date' => 'nullable|date|after_or_equal:today',
        ], [
            'name.required' => 'Nama project wajib diisi.',
            'name.min' => 'Nama minimal 3 karakter.',
            'due_date.after_or_equal' => 'Deadline tidak boleh di masa lalu.',
        ]);

        // Sanitize
        $validated['name'] = strip_tags(trim($validated['name']));
        $validated['description'] = $validated['description'] ? strip_tags($validated['description']) : null;

        $project = ProjectKanban::create($validated);

        // Notify admins
        KanbanNotificationService::notifyProjectCreated($project, Auth::user());

        return redirect()
            ->route('kanban.projects.show', $project)
            ->with('success', 'Project berhasil dibuat.');
    }

    /**
     * Kanban Board View - display assets grouped by stage
     */
    public function show(ProjectKanban $project)
    {
        $project->load([
            'client:id,name,company_name',
            'assets' => fn($q) => $q
                ->select('id', 'project_id', 'name', 'asset_code', 'current_stage', 'priority', 'position')
                ->orderBy('position')
        ]);

        $stages = ProjectAssetKanban::STAGES;
        
        // Group assets by stage (data already loaded)
        $assetsByStage = collect($stages)->mapWithKeys(fn($label, $num) => [
            $num => $project->assets->where('current_stage', $num)->values()
        ]);

        return view('kanban.projects.show', compact('project', 'stages', 'assetsByStage'));
    }

    /**
     * Show edit form
     */
    public function edit(ProjectKanban $project)
    {
        $clients = ClientKanban::select('id', 'name', 'company_name')
            ->orderBy('name')
            ->get();

        return view('kanban.projects.edit', compact('project', 'clients'));
    }

    /**
     * Update project with validation & notification on completion
     */
    public function update(Request $request, ProjectKanban $project)
    {
        $validated = $request->validate([
            'client_id' => 'required|exists:clients_kanban,id',
            'name' => 'required|string|max:255|min:3',
            'description' => 'nullable|string|max:2000',
            'due_date' => 'nullable|date',
            'status' => 'required|in:active,completed,cancelled',
        ]);

        $validated['name'] = strip_tags(trim($validated['name']));
        $validated['description'] = $validated['description'] ? strip_tags($validated['description']) : null;

        $oldStatus = $project->status;
        $project->update($validated);

        // Notify if project completed
        if ($validated['status'] === 'completed' && $oldStatus !== 'completed') {
            $this->notifyAdmins('project_completed', [
                'title' => 'Project Selesai',
                'message' => Auth::user()->name . " menyelesaikan project: {$project->name}",
                'action_url' => route('kanban.projects.show', $project->id),
            ]);
        }

        return redirect()
            ->route('kanban.projects.show', $project)
            ->with('success', 'Project berhasil diupdate.');
    }

    /**
     * Soft delete project
     */
    public function destroy(ProjectKanban $project)
    {
        $projectName = $project->name;
        $project->delete();

        return redirect()
            ->route('kanban.projects.index')
            ->with('success', "Project '{$projectName}' berhasil dihapus.");
    }

    /**
     * API: Get statistics with optimized queries
     */
    public function statistics()
    {
        // Single query for all project stats
        $projectStats = ProjectKanban::toBase()
            ->selectRaw("COUNT(*) as total")
            ->selectRaw("SUM(CASE WHEN status = 'active' THEN 1 ELSE 0 END) as active")
            ->selectRaw("SUM(CASE WHEN status = 'completed' THEN 1 ELSE 0 END) as completed")
            ->selectRaw("SUM(CASE WHEN status = 'active' AND due_date IS NOT NULL AND due_date < NOW() THEN 1 ELSE 0 END) as overdue")
            ->first();

        // Single query for all stage counts
        $stageCounts = ProjectAssetKanban::toBase()
            ->select('current_stage', DB::raw('COUNT(*) as count'))
            ->groupBy('current_stage')
            ->pluck('count', 'current_stage');

        $assetsByStage = collect(ProjectAssetKanban::STAGES)->mapWithKeys(fn($label, $num) => [
            $num => ['label' => $label, 'count' => $stageCounts->get($num, 0)]
        ]);

        return response()->json([
            'projects' => [
                'total' => (int) $projectStats->total,
                'active' => (int) $projectStats->active,
                'completed' => (int) $projectStats->completed,
                'overdue' => (int) $projectStats->overdue,
            ],
            'assets_by_stage' => $assetsByStage,
        ]);
    }

    /**
     * Helper: Notify all admins
     */
    private function notifyAdmins(string $type, array $data): void
    {
        User::where('id', '!=', Auth::id())->chunk(100, function ($admins) use ($type, $data) {
            foreach ($admins as $admin) {
                Notification::notify($admin, $type, $data);
            }
        });
    }
}

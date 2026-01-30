<?php

namespace App\Http\Controllers\Appraisal;

use App\Http\Controllers\Controller;
use App\Models\ProjectKanban;
use App\Models\ProjectAsset;
use App\Models\ActivityKanban;
use Illuminate\Http\Request;

class ActivityKanbanController extends Controller
{
    /**
     * Display a listing of activities (global activity log)
     */
    public function index(Request $request)
    {
        $query = ActivityKanban::with(['project.client', 'asset', 'user']);

        if ($request->filled('activity_type')) {
            $query->where('activity_type', $request->activity_type);
        }

        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        if ($request->filled('project_id')) {
            $query->where('project_id', $request->project_id);
        }

        if ($request->filled('asset_id')) {
            $query->where('project_asset_id', $request->asset_id);
        }

        if ($request->filled('level')) {
            if ($request->level === 'project') {
                $query->projectLevel();
            } else {
                $query->assetLevel();
            }
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $activities = $query->orderBy('created_at', 'desc')->paginate(30);
        $activityTypes = ActivityKanban::TYPES;
        $projects = ProjectKanban::with('client')->orderBy('name')->get();

        return view('appraisal.activities.index', compact('activities', 'activityTypes', 'projects'));
    }

    // ==========================================
    // PROJECT LEVEL ACTIVITIES
    // ==========================================

    /**
     * Store a new comment activity for PROJECT
     */
    public function storeComment(Request $request, ProjectKanban $project)
    {
        $validated = $request->validate([
            'comment' => 'required|string|max:1000',
        ]);

        $activity = ActivityKanban::logComment($project, $request->user(), $validated['comment']);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'activity' => $activity->load('user'),
            ]);
        }

        return back()->with('success', 'Komentar berhasil ditambahkan.');
    }

    /**
     * Store an obstacle report for PROJECT
     */
    public function storeObstacle(Request $request, ProjectKanban $project)
    {
        $validated = $request->validate([
            'description' => 'required|string|max:1000',
        ]);

        // Set project to warning status
        $project->update(['priority_status' => 'warning']);

        $activity = ActivityKanban::logObstacle($project, $request->user(), $validated['description']);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'activity' => $activity->load('user'),
            ]);
        }

        return back()->with('warning', 'Laporan halangan berhasil dicatat.');
    }

    /**
     * Resolve an obstacle for PROJECT
     */
    public function resolveObstacle(Request $request, ProjectKanban $project)
    {
        $validated = $request->validate([
            'resolution' => 'required|string|max:1000',
        ]);

        // Reset priority status if no other issues
        $project->update(['priority_status' => 'normal']);

        $activity = ActivityKanban::create([
            'project_id' => $project->id,
            'project_asset_id' => null,
            'user_id' => $request->user()->id,
            'activity_type' => 'comment',
            'stage_context' => $project->current_stage,
            'description' => 'Halangan diselesaikan: ' . $validated['resolution'],
        ]);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'activity' => $activity->load('user'),
            ]);
        }

        return back()->with('success', 'Halangan berhasil diselesaikan.');
    }

    /**
     * Get activities for a specific project (AJAX)
     */
    public function projectActivities(ProjectKanban $project, Request $request)
    {
        $query = ActivityKanban::where('project_id', $project->id)
            ->with(['user', 'asset']);

        if ($request->filled('type')) {
            $query->where('activity_type', $request->type);
        }

        if ($request->boolean('project_only')) {
            $query->projectLevel();
        }

        $activities = $query->orderBy('created_at', 'desc')
            ->paginate($request->get('per_page', 20));

        return response()->json($activities);
    }

    // ==========================================
    // ASSET LEVEL ACTIVITIES
    // ==========================================

    /**
     * Store a new comment activity for ASSET
     */
    public function storeAssetComment(Request $request, ProjectAsset $asset)
    {
        $validated = $request->validate([
            'comment' => 'required|string|max:1000',
        ]);

        $activity = ActivityKanban::logAssetComment($asset, $request->user(), $validated['comment']);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'activity' => $activity->load(['user', 'asset']),
            ]);
        }

        return back()->with('success', 'Komentar berhasil ditambahkan.');
    }

    /**
     * Get activities for a specific asset (AJAX)
     */
    public function assetActivities(ProjectAsset $asset, Request $request)
    {
        $query = ActivityKanban::where('project_asset_id', $asset->id)
            ->with('user');

        if ($request->filled('type')) {
            $query->where('activity_type', $request->type);
        }

        $activities = $query->orderBy('created_at', 'desc')
            ->paginate($request->get('per_page', 20));

        return response()->json($activities);
    }

    // ==========================================
    // SHARED / ANALYTICS
    // ==========================================

    /**
     * Get recent activities for dashboard
     */
    public function recent(Request $request)
    {
        $limit = $request->get('limit', 10);

        $activities = ActivityKanban::with(['project.client', 'asset', 'user'])
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();

        return response()->json($activities);
    }

    /**
     * Get activity statistics
     */
    public function statistics(Request $request)
    {
        $startDate = $request->get('start_date', now()->subDays(30));
        $endDate = $request->get('end_date', now());

        $stats = [
            'total' => ActivityKanban::whereBetween('created_at', [$startDate, $endDate])->count(),
            'project_level' => ActivityKanban::whereBetween('created_at', [$startDate, $endDate])
                ->projectLevel()->count(),
            'asset_level' => ActivityKanban::whereBetween('created_at', [$startDate, $endDate])
                ->assetLevel()->count(),
            'by_type' => [],
            'by_user' => ActivityKanban::whereBetween('created_at', [$startDate, $endDate])
                ->selectRaw('user_id, COUNT(*) as count')
                ->groupBy('user_id')
                ->with('user:id,name')
                ->get(),
        ];

        foreach (ActivityKanban::TYPES as $type => $label) {
            $stats['by_type'][$type] = ActivityKanban::whereBetween('created_at', [$startDate, $endDate])
                ->where('activity_type', $type)
                ->count();
        }

        return response()->json($stats);
    }

    /**
     * Delete an activity (admin only)
     */
    public function destroy(ActivityKanban $activity)
    {
        $project = $activity->project;
        $activity->delete();

        return back()->with('success', 'Aktivitas berhasil dihapus.');
    }
}

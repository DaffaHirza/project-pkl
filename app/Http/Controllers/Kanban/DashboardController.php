<?php

namespace App\Http\Controllers\Kanban;

use App\Http\Controllers\Controller;
use App\Models\ClientKanban;
use App\Models\ProjectKanban;
use App\Models\ProjectAssetKanban;
use App\Models\AssetNoteKanban;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class DashboardController extends Controller
{
    public function index()
    {
        // Single query for project status counts
        $projectStats = ProjectKanban::toBase()
            ->selectRaw("COUNT(*) as total")
            ->selectRaw("SUM(CASE WHEN status = 'active' THEN 1 ELSE 0 END) as active")
            ->selectRaw("SUM(CASE WHEN status = 'active' AND due_date IS NOT NULL AND due_date < NOW() THEN 1 ELSE 0 END) as overdue")
            ->first();

        // Single query for asset counts
        $assetStats = ProjectAssetKanban::toBase()
            ->selectRaw("COUNT(*) as total")
            ->selectRaw("SUM(CASE WHEN current_stage = 13 THEN 1 ELSE 0 END) as completed")
            ->selectRaw("SUM(CASE WHEN priority = 'critical' THEN 1 ELSE 0 END) as critical")
            ->first();

        // Single query: Get all stage counts at once
        $stageCounts = ProjectAssetKanban::toBase()
            ->select('current_stage', DB::raw('COUNT(*) as count'))
            ->groupBy('current_stage')
            ->pluck('count', 'current_stage');

        // Build stats array with proper keys for view
        $stats = [
            'total_clients' => ClientKanban::count(),
            'total_projects' => (int) $projectStats->total,
            'active_projects' => (int) $projectStats->active,
            'overdue_projects' => (int) $projectStats->overdue,
            'total_assets' => (int) $assetStats->total,
            'completed_assets' => (int) $assetStats->completed,
            'critical_count' => (int) $assetStats->critical,
            'assets_by_stage' => $stageCounts->toArray(),
        ];

        // Critical Assets
        $criticalAssets = ProjectAssetKanban::with('project:id,name,project_code')
            ->select('id', 'project_id', 'name', 'asset_code', 'current_stage', 'priority', 'updated_at')
            ->where('priority', 'critical')
            ->where('current_stage', '<', 13)
            ->latest('updated_at')
            ->limit(10)
            ->get();

        // Recent Activities (stage changes from notes)
        $recentActivities = AssetNoteKanban::with(['user:id,name', 'asset:id,name,asset_code'])
            ->select('id', 'asset_id', 'user_id', 'stage', 'type', 'content', 'created_at')
            ->latest()
            ->limit(10)
            ->get();

        return view('kanban.dashboard', compact(
            'stats',
            'criticalAssets',
            'recentActivities'
        ));
    }

    // API: Get dashboard data (optimized single queries)
    public function data()
    {
        // Single query for project stats
        $projectStats = ProjectKanban::toBase()
            ->selectRaw("COUNT(*) as total")
            ->selectRaw("SUM(CASE WHEN status = 'active' THEN 1 ELSE 0 END) as active")
            ->selectRaw("SUM(CASE WHEN status = 'completed' THEN 1 ELSE 0 END) as completed")
            ->selectRaw("SUM(CASE WHEN status = 'active' AND due_date IS NOT NULL AND due_date < NOW() THEN 1 ELSE 0 END) as overdue")
            ->first();

        // Single query for asset stats
        $assetStats = ProjectAssetKanban::toBase()
            ->selectRaw("COUNT(*) as total")
            ->selectRaw("SUM(CASE WHEN current_stage = 13 THEN 1 ELSE 0 END) as completed")
            ->first();

        $stats = [
            'clients' => ClientKanban::count(),
            'projects' => [
                'total' => (int) $projectStats->total,
                'active' => (int) $projectStats->active,
                'completed' => (int) $projectStats->completed,
                'overdue' => (int) $projectStats->overdue,
            ],
            'assets' => [
                'total' => (int) $assetStats->total,
                'completed' => (int) $assetStats->completed,
                'in_progress' => (int) $assetStats->total - (int) $assetStats->completed,
            ],
        ];

        // Single query for all stage counts
        $stageCounts = ProjectAssetKanban::toBase()
            ->select('current_stage', DB::raw('COUNT(*) as count'))
            ->groupBy('current_stage')
            ->pluck('count', 'current_stage');

        $assetsByStage = collect(ProjectAssetKanban::STAGES)->mapWithKeys(fn($label, $num) => [
            $num => ['label' => $label, 'count' => $stageCounts->get($num, 0)]
        ]);

        return response()->json([
            'stats' => $stats,
            'assets_by_stage' => $assetsByStage,
        ]);
    }

    // Activity Log page - all activities across all assets
    public function activityLog()
    {
        $query = AssetNoteKanban::with(['user:id,name', 'asset:id,name,asset_code,project_id', 'asset.project:id,name,project_code'])
            ->select('id', 'asset_id', 'user_id', 'stage', 'type', 'content', 'created_at');

        // Filter by type
        if (request('type')) {
            $query->where('type', request('type'));
        }

        // Filter by stage
        if (request('stage')) {
            $query->where('stage', request('stage'));
        }

        // Filter by date range
        if (request('from')) {
            $query->whereDate('created_at', '>=', request('from'));
        }
        if (request('to')) {
            $query->whereDate('created_at', '<=', request('to'));
        }

        // Search
        if (request('search')) {
            $search = request('search');
            $query->where(function($q) use ($search) {
                $q->where('content', 'like', "%{$search}%")
                  ->orWhereHas('asset', fn($q2) => $q2->where('name', 'like', "%{$search}%")->orWhere('asset_code', 'like', "%{$search}%"))
                  ->orWhereHas('user', fn($q2) => $q2->where('name', 'like', "%{$search}%"));
            });
        }

        $activities = $query->latest()->paginate(50)->withQueryString();
        $stages = ProjectAssetKanban::STAGES;
        $types = ['note' => 'Catatan', 'stage_change' => 'Perubahan Stage', 'approval' => 'Approval', 'rejection' => 'Rejection'];

        return view('kanban.activity-log', compact('activities', 'stages', 'types'));
    }
}

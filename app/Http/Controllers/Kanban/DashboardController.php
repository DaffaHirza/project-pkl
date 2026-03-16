<?php

namespace App\Http\Controllers\Kanban;

use App\Http\Controllers\Controller;
use App\Models\ClientKanban;
use App\Models\AssetKanban;
use App\Models\AssetNoteKanban;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        // Single query for client counts by type
        $clientStats = ClientKanban::toBase()
            ->selectRaw("COUNT(*) as total")
            ->selectRaw("SUM(CASE WHEN type = 'bank' THEN 1 ELSE 0 END) as banks")
            ->selectRaw("SUM(CASE WHEN type = 'pt_cv' THEN 1 ELSE 0 END) as pt_cvs")
            ->selectRaw("SUM(CASE WHEN type = 'debitur' THEN 1 ELSE 0 END) as debiturs")
            ->first();

        // Single query for asset counts
        $assetStats = AssetKanban::toBase()
            ->selectRaw("COUNT(*) as total")
            ->selectRaw("SUM(CASE WHEN current_stage = 13 THEN 1 ELSE 0 END) as completed")
            ->selectRaw("SUM(CASE WHEN priority = 'critical' THEN 1 ELSE 0 END) as critical")
            ->selectRaw("SUM(CASE WHEN priority = 'high' THEN 1 ELSE 0 END) as high")
            ->selectRaw("SUM(CASE WHEN priority = 'normal' THEN 1 ELSE 0 END) as normal")
            ->selectRaw("SUM(CASE WHEN priority = 'low' THEN 1 ELSE 0 END) as low")
            ->first();

        // Single query: Get all stage counts at once
        $stageCounts = AssetKanban::toBase()
            ->select('current_stage', DB::raw('COUNT(*) as count'))
            ->groupBy('current_stage')
            ->pluck('count', 'current_stage');

        // Build stats array with proper keys for view
        $stats = [
            'total_clients' => (int) $clientStats->total,
            'total_banks' => (int) $clientStats->banks,
            'total_pt_cvs' => (int) $clientStats->pt_cvs,
            'total_debiturs' => (int) $clientStats->debiturs,
            'total_assets' => (int) $assetStats->total,
            'completed_assets' => (int) $assetStats->completed,
            'critical_count' => (int) $assetStats->critical,
            'high_count' => (int) $assetStats->high,
            'normal_count' => (int) $assetStats->normal,
            'low_count' => (int) $assetStats->low,
            'assets_by_stage' => $stageCounts->toArray(),
        ];

        // Critical Assets
        $criticalAssets = AssetKanban::with('client:id,name,company_name,type')
            ->select('id', 'client_id', 'name', 'current_stage', 'priority', 'updated_at')
            ->where('priority', 'critical')
            ->where('current_stage', '<', 13)
            ->latest('updated_at')
            ->limit(10)
            ->get();

        // Recent Activities (stage changes from notes)
        $recentActivities = AssetNoteKanban::with(['user:id,name', 'asset:id,name'])
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

    // Activity Log page - all activities across all assets
    public function activityLog()
    {
        $query = AssetNoteKanban::with(['user:id,name', 'asset:id,name,client_id', 'asset.client:id,name,company_name'])
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
                  ->orWhereHas('asset', fn($q2) => $q2->where('name', 'like', "%{$search}%"))
                  ->orWhereHas('user', fn($q2) => $q2->where('name', 'like', "%{$search}%"));
            });
        }

        $activities = $query->latest()->paginate(50)->withQueryString();
        $stages = AssetKanban::STAGES;
        $types = ['note' => 'Catatan', 'stage_change' => 'Perubahan Stage', 'approval' => 'Approval', 'rejection' => 'Rejection'];

        return view('kanban.activity-log', compact('activities', 'stages', 'types'));
    }
}

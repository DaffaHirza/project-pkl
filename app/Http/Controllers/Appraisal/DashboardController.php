<?php

namespace App\Http\Controllers\Appraisal;

use App\Http\Controllers\Controller;
use App\Models\ProjectKanban;
use App\Models\ProjectAsset;
use App\Models\KanbanClient;
use App\Models\InvoiceKanban;
use App\Models\ActivityKanban;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

class DashboardController extends Controller
{
    /**
     * Display the appraisal dashboard
     */
    public function index()
    {
        // Project statistics by stage
        $projectsByStage = [];
        foreach (ProjectKanban::STAGES as $stage => $label) {
            $projectsByStage[$stage] = [
                'label' => $label,
                'count' => ProjectKanban::where('current_stage', $stage)->count(),
            ];
        }

        // Asset (Technical Level) statistics by stage
        $assetsByStage = [];
        if (Schema::hasTable('project_assets_kanban')) {
            foreach (ProjectAsset::STAGES as $stage => $label) {
                $assetsByStage[$stage] = [
                    'label' => $label,
                    'count' => ProjectAsset::where('current_stage', $stage)->count(),
                ];
            }
        }

        // Project priority statistics
        $projectPriorityStats = [
            'normal' => ProjectKanban::where('priority_status', 'normal')
                ->where('current_stage', '!=', 'done')->count(),
            'warning' => ProjectKanban::where('priority_status', 'warning')->count(),
            'critical' => ProjectKanban::where('priority_status', 'critical')->count(),
        ];

        // Asset priority statistics
        $assetPriorityStats = [
            'normal' => 0,
            'warning' => 0,
            'critical' => 0,
        ];
        if (Schema::hasTable('project_assets_kanban')) {
            $assetPriorityStats = [
                'normal' => ProjectAsset::where('priority_status', 'normal')
                    ->where('current_stage', '!=', 'done')->count(),
                'warning' => ProjectAsset::where('priority_status', 'warning')->count(),
                'critical' => ProjectAsset::where('priority_status', 'critical')->count(),
            ];
        }

        // Overdue projects
        $overdueProjects = ProjectKanban::with('client')
            ->whereNotNull('due_date')
            ->where('due_date', '<', now())
            ->where('current_stage', '!=', 'done')
            ->orderBy('due_date')
            ->limit(5)
            ->get();

        // Overdue assets
        $overdueAssets = [];
        if (Schema::hasTable('project_assets_kanban')) {
            $overdueAssets = ProjectAsset::with('project.client')
                ->whereNotNull('target_completion_date')
                ->where('target_completion_date', '<', now())
                ->where('current_stage', '!=', 'done')
                ->orderBy('target_completion_date')
                ->limit(5)
                ->get();
        }

        // Overdue invoices
        $overdueInvoices = InvoiceKanban::with('project.client')
            ->overdue()
            ->orderBy('payment_due_date')
            ->limit(5)
            ->get();

        // Recent activities (both project and asset level)
        $recentActivities = ActivityKanban::with(['project.client', 'asset', 'user'])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        // Quick stats
        $hasAssetTable = Schema::hasTable('project_assets_kanban');
        
        $stats = [
            // Project stats
            'total_projects' => ProjectKanban::count(),
            'ongoing_projects' => ProjectKanban::where('current_stage', '!=', 'done')->count(),
            'active_projects' => ProjectKanban::where('current_stage', '!=', 'done')->count(),
            'completed_projects' => ProjectKanban::where('current_stage', 'done')->count(),
            'completed_this_month' => ProjectKanban::where('current_stage', 'done')
                ->whereMonth('updated_at', now()->month)->count(),
            
            // Asset stats
            'total_assets' => $hasAssetTable ? ProjectAsset::count() : 0,
            'active_assets' => $hasAssetTable ? ProjectAsset::where('current_stage', '!=', 'done')->count() : 0,
            'completed_assets' => $hasAssetTable ? ProjectAsset::where('current_stage', 'done')->count() : 0,
            'assets_completed_this_month' => $hasAssetTable 
                ? ProjectAsset::where('current_stage', 'done')
                    ->whereMonth('updated_at', now()->month)
                    ->count() 
                : 0,
            
            // Other stats
            'total_clients' => KanbanClient::count(),
            'unpaid_invoices' => InvoiceKanban::unpaid()->count(),
        ];

        return view('appraisal.dashboard', compact(
            'projectsByStage',
            'assetsByStage',
            'overdueProjects',
            'overdueAssets',
            'overdueInvoices',
            'recentActivities',
            'stats'
        ))->with('priorityStats', $projectPriorityStats);
    }

    /**
     * Get dashboard data for AJAX refresh
     */
    public function data()
    {
        $data = [
            // Project level
            'projects_by_stage' => [],
            'project_priority_stats' => [
                'normal' => ProjectKanban::where('priority_status', 'normal')
                    ->where('current_stage', '!=', 'done')->count(),
                'warning' => ProjectKanban::where('priority_status', 'warning')->count(),
                'critical' => ProjectKanban::where('priority_status', 'critical')->count(),
            ],
            
            // Asset level
            'assets_by_stage' => [],
            'asset_priority_stats' => [
                'normal' => ProjectAsset::where('priority_status', 'normal')
                    ->where('current_stage', '!=', 'done')->count(),
                'warning' => ProjectAsset::where('priority_status', 'warning')->count(),
                'critical' => ProjectAsset::where('priority_status', 'critical')->count(),
            ],
            
            // Counts
            'overdue_projects_count' => ProjectKanban::whereNotNull('due_date')
                ->where('due_date', '<', now())
                ->where('current_stage', '!=', 'done')
                ->count(),
            'overdue_assets_count' => ProjectAsset::whereNotNull('target_completion_date')
                ->where('target_completion_date', '<', now())
                ->where('current_stage', '!=', 'done')
                ->count(),
            'unpaid_invoices' => InvoiceKanban::unpaid()->count(),
        ];

        foreach (ProjectKanban::STAGES as $stage => $label) {
            $data['projects_by_stage'][$stage] = ProjectKanban::where('current_stage', $stage)->count();
        }

        foreach (ProjectAsset::STAGES as $stage => $label) {
            $data['assets_by_stage'][$stage] = ProjectAsset::where('current_stage', $stage)->count();
        }

        return response()->json($data);
    }

    /**
     * Get projects needing attention
     */
    public function needsAttention()
    {
        // Projects needing attention
        $projects = ProjectKanban::with('client')
            ->where(function ($q) {
                $q->where('priority_status', 'critical')
                  ->orWhere('priority_status', 'warning')
                  ->orWhere(function ($q2) {
                      $q2->whereNotNull('due_date')
                         ->where('due_date', '<', now())
                         ->where('current_stage', '!=', 'done');
                  });
            })
            ->orderByRaw("CASE priority_status WHEN 'critical' THEN 1 WHEN 'warning' THEN 2 ELSE 3 END")
            ->orderBy('due_date')
            ->get();

        // Assets needing attention
        $assets = ProjectAsset::with('project.client')
            ->where(function ($q) {
                $q->where('priority_status', 'critical')
                  ->orWhere('priority_status', 'warning')
                  ->orWhere(function ($q2) {
                      $q2->whereNotNull('target_completion_date')
                         ->where('target_completion_date', '<', now())
                         ->where('current_stage', '!=', 'done');
                  });
            })
            ->orderByRaw("CASE priority_status WHEN 'critical' THEN 1 WHEN 'warning' THEN 2 ELSE 3 END")
            ->orderBy('target_completion_date')
            ->get();

        return response()->json([
            'projects' => $projects,
            'assets' => $assets,
        ]);
    }

    /**
     * Get workflow summary (separated for projects and assets)
     */
    public function workflowSummary()
    {
        // Project workflow
        $projectSummary = [];
        foreach (ProjectKanban::STAGES as $stage => $label) {
            $projects = ProjectKanban::where('current_stage', $stage)
                ->with('client')
                ->orderBy('priority_status', 'desc')
                ->orderBy('due_date')
                ->get();

            $projectSummary[$stage] = [
                'label' => $label,
                'count' => $projects->count(),
                'critical' => $projects->where('priority_status', 'critical')->count(),
                'warning' => $projects->where('priority_status', 'warning')->count(),
                'items' => $projects->take(5),
            ];
        }

        // Technical workflow (assets)
        $technicalSummary = [];
        foreach (ProjectAsset::STAGES as $stage => $label) {
            $assets = ProjectAsset::where('current_stage', $stage)
                ->with('project.client')
                ->orderBy('priority_status', 'desc')
                ->orderBy('target_completion_date')
                ->get();

            $technicalSummary[$stage] = [
                'label' => $label,
                'count' => $assets->count(),
                'critical' => $assets->where('priority_status', 'critical')->count(),
                'warning' => $assets->where('priority_status', 'warning')->count(),
                'items' => $assets->take(5),
            ];
        }

        return response()->json([
            'project_workflow' => $projectSummary,
            'technical_workflow' => $technicalSummary,
        ]);
    }
}

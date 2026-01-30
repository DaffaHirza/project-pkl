<?php

namespace App\Http\Controllers\Appraisal;

use App\Http\Controllers\Controller;
use App\Models\ProjectKanban;
use App\Models\ProjectAsset;
use App\Models\ReportKanban;
use App\Models\ActivityKanban;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ReportKanbanController extends Controller
{
    /**
     * Display a listing of reports
     */
    public function index(Request $request)
    {
        $query = ReportKanban::with(['asset.project.client']);

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('is_approved')) {
            $query->where('is_approved', $request->is_approved === 'yes');
        }

        if ($request->filled('project_id')) {
            $query->whereHas('asset', fn($q) => $q->where('project_id', $request->project_id));
        }

        $reports = $query->orderBy('created_at', 'desc')->paginate(20);
        $projects = ProjectKanban::with('client')->ongoing()->orderBy('name')->get();

        return view('appraisal.reports.index', compact('reports', 'projects'));
    }

    /**
     * Show the form for creating a new report (now for an ASSET)
     */
    public function create(ProjectAsset $asset)
    {
        $asset->load('project.client');
        $types = ReportKanban::TYPES;
        return view('appraisal.reports.create', compact('asset', 'types'));
    }

    /**
     * Store a newly created report (now linked to ASSET)
     */
    public function store(Request $request, ProjectAsset $asset)
    {
        $validated = $request->validate([
            'type' => 'required|in:working_paper,draft_report,final_report',
            'report_file' => 'required|file|mimes:pdf,doc,docx|max:20480',
        ]);

        // Get latest version for this type on this asset
        $latestVersion = ReportKanban::where('project_asset_id', $asset->id)
            ->where('type', $validated['type'])
            ->max('version') ?? 0;

        $filePath = $request->file('report_file')->store('reports/' . $asset->project_id . '/' . $asset->id, 'public');

        $report = ReportKanban::create([
            'project_asset_id' => $asset->id,
            'project_id' => $asset->project_id, // Keep for backward compatibility
            'type' => $validated['type'],
            'file_path' => $filePath,
            'version' => $latestVersion + 1,
            'is_approved' => false,
        ]);

        // Log activity
        ActivityKanban::create([
            'project_id' => $asset->project_id,
            'project_asset_id' => $asset->id,
            'user_id' => $request->user()->id,
            'activity_type' => 'upload',
            'stage_context' => $asset->current_stage,
            'description' => ReportKanban::TYPES[$validated['type']] . " v{$report->version} untuk '{$asset->name}' diupload.",
        ]);

        return redirect()
            ->route('appraisal.assets.show', $asset)
            ->with('success', 'Laporan berhasil diupload.');
    }

    /**
     * Display the specified report
     */
    public function show(ReportKanban $report)
    {
        $report->load(['asset.project.client']);
        return view('appraisal.reports.show', compact('report'));
    }

    /**
     * Upload new version of report
     */
    public function uploadVersion(Request $request, ReportKanban $report)
    {
        $validated = $request->validate([
            'report_file' => 'required|file|mimes:pdf,doc,docx|max:20480',
        ]);

        $asset = $report->asset;
        $filePath = $request->file('report_file')->store('reports/' . $asset->project_id . '/' . $asset->id, 'public');

        $newReport = ReportKanban::create([
            'project_asset_id' => $asset->id,
            'project_id' => $asset->project_id,
            'type' => $report->type,
            'file_path' => $filePath,
            'version' => $report->version + 1,
            'is_approved' => false,
        ]);

        ActivityKanban::create([
            'project_id' => $asset->project_id,
            'project_asset_id' => $asset->id,
            'user_id' => $request->user()->id,
            'activity_type' => 'upload',
            'stage_context' => $asset->current_stage,
            'description' => ReportKanban::TYPES[$report->type] . " v{$newReport->version} diupload (revisi).",
        ]);

        return redirect()
            ->route('appraisal.assets.show', $asset)
            ->with('success', 'Versi baru laporan berhasil diupload.');
    }

    /**
     * Approve report (internal or move to client approval)
     * Now operates on ASSET stage, not project
     */
    public function approve(Request $request, ReportKanban $report)
    {
        $report->update(['is_approved' => true]);

        $asset = $report->asset;

        // If draft report approved in review stage, move to client approval
        if ($report->type === 'draft_report' && $asset->current_stage === 'review') {
            $asset->update(['current_stage' => 'client_approval']);
            ActivityKanban::logAssetStageMove($asset, $request->user(), 'review', 'client_approval');
        }

        // If final report approved, move asset to done
        if ($report->type === 'final_report' && $asset->current_stage === 'final_report') {
            $asset->update(['current_stage' => 'done']);
            ActivityKanban::logAssetStageMove($asset, $request->user(), 'final_report', 'done');
            ActivityKanban::logAssetCompleted($asset, $request->user());
            
            // Update parent project progress
            $asset->project->updateProgress();
        }

        ActivityKanban::create([
            'project_id' => $asset->project_id,
            'project_asset_id' => $asset->id,
            'user_id' => $request->user()->id,
            'activity_type' => 'approval',
            'stage_context' => $asset->current_stage,
            'description' => ReportKanban::TYPES[$report->type] . " v{$report->version} untuk '{$asset->name}' disetujui.",
        ]);

        return redirect()
            ->route('appraisal.assets.show', $asset)
            ->with('success', 'Laporan berhasil disetujui.');
    }

    /**
     * Request revision for report
     */
    public function requestRevision(Request $request, ReportKanban $report)
    {
        $validated = $request->validate([
            'revision_notes' => 'required|string',
        ]);

        $asset = $report->asset;
        $asset->update(['priority_status' => 'warning']);

        ActivityKanban::create([
            'project_id' => $asset->project_id,
            'project_asset_id' => $asset->id,
            'user_id' => $request->user()->id,
            'activity_type' => 'rejection',
            'stage_context' => $asset->current_stage,
            'description' => "Revisi diminta untuk " . ReportKanban::TYPES[$report->type] . " v{$report->version}: " . $validated['revision_notes'],
        ]);

        return redirect()
            ->route('appraisal.assets.show', $asset)
            ->with('warning', 'Permintaan revisi berhasil dikirim.');
    }

    /**
     * Remove the specified report
     */
    public function destroy(Request $request, ReportKanban $report)
    {
        $asset = $report->asset;
        
        // Delete file
        if ($report->file_path) {
            Storage::disk('public')->delete($report->file_path);
        }

        $reportInfo = ReportKanban::TYPES[$report->type] . " v{$report->version}";
        $report->delete();

        ActivityKanban::create([
            'project_id' => $asset->project_id,
            'project_asset_id' => $asset->id,
            'user_id' => $request->user()->id,
            'activity_type' => 'comment',
            'stage_context' => $asset->current_stage,
            'description' => "{$reportInfo} dihapus.",
        ]);

        return redirect()
            ->route('appraisal.assets.show', $asset)
            ->with('success', 'Laporan berhasil dihapus.');
    }

    /**
     * Download report file
     */
    public function download(ReportKanban $report)
    {
        if (!$report->file_path || !Storage::disk('public')->exists($report->file_path)) {
            return back()->with('error', 'File tidak ditemukan.');
        }

        $asset = $report->asset;
        $fileName = $asset->asset_code . '_' . $report->type . '_v' . $report->version . '.' . pathinfo($report->file_path, PATHINFO_EXTENSION);

        $filePath = Storage::disk('public')->path($report->file_path);

        return response()->download($filePath, $fileName);
    }
}

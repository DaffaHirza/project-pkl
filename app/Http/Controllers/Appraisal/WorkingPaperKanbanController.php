<?php

namespace App\Http\Controllers\Appraisal;

use App\Http\Controllers\Controller;
use App\Models\ProjectKanban;
use App\Models\ProjectAsset;
use App\Models\WorkingPaperKanban;
use App\Models\ActivityKanban;
use App\Models\User;
use Illuminate\Http\Request;

class WorkingPaperKanbanController extends Controller
{
    /**
     * Display a listing of working papers
     */
    public function index(Request $request)
    {
        $query = WorkingPaperKanban::with(['asset.project.client', 'analyst']);

        if ($request->filled('analyst_id')) {
            $query->where('analyst_id', $request->analyst_id);
        }

        if ($request->filled('project_id')) {
            $query->whereHas('asset', fn($q) => $q->where('project_id', $request->project_id));
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $workingPapers = $query->orderBy('created_at', 'desc')->paginate(20);
        $analysts = User::orderBy('name')->get();
        $projects = ProjectKanban::with('client')->ongoing()->orderBy('name')->get();

        return view('appraisal.working-papers.index', compact('workingPapers', 'analysts', 'projects'));
    }

    /**
     * Show the form for creating a new working paper (now for an ASSET)
     */
    public function create(ProjectAsset $asset)
    {
        $asset->load('project.client');
        $users = User::orderBy('name')->get();
        $methodologies = WorkingPaperKanban::METHODOLOGIES;
        return view('appraisal.working-papers.create', compact('asset', 'users', 'methodologies'));
    }

    /**
     * Store a newly created working paper (now linked to ASSET)
     */
    public function store(Request $request, ProjectAsset $asset)
    {
        $validated = $request->validate([
            'analyst_id' => 'required|exists:users,id',
            'methodology' => 'nullable|in:' . implode(',', array_keys(WorkingPaperKanban::METHODOLOGIES)),
            'assessed_value' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string',
        ]);

        $validated['project_asset_id'] = $asset->id;
        // Keep project_id for backward compatibility
        $validated['project_id'] = $asset->project_id;
        $validated['status'] = 'draft';

        $workingPaper = WorkingPaperKanban::create($validated);

        // Log activity
        $analyst = User::find($validated['analyst_id']);
        ActivityKanban::create([
            'project_id' => $asset->project_id,
            'project_asset_id' => $asset->id,
            'user_id' => $request->user()->id,
            'activity_type' => 'comment',
            'stage_context' => 'analysis',
            'description' => "Kertas kerja untuk '{$asset->name}' dibuat. Analis: {$analyst->name}.",
        ]);

        return redirect()
            ->route('appraisal.assets.show', $asset)
            ->with('success', 'Kertas kerja berhasil dibuat.');
    }

    /**
     * Update the specified working paper
     */
    public function update(Request $request, WorkingPaperKanban $workingPaper)
    {
        $validated = $request->validate([
            'analyst_id' => 'required|exists:users,id',
            'methodology' => 'nullable|in:' . implode(',', array_keys(WorkingPaperKanban::METHODOLOGIES)),
            'assessed_value' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string',
        ]);

        $workingPaper->update($validated);

        return redirect()
            ->route('appraisal.assets.show', $workingPaper->asset)
            ->with('success', 'Kertas kerja berhasil diperbarui.');
    }

    /**
     * Mark working paper as completed and move asset to review stage
     */
    public function complete(Request $request, WorkingPaperKanban $workingPaper)
    {
        $workingPaper->update(['status' => 'completed']);

        $asset = $workingPaper->asset;

        // Move asset to review stage (not project)
        if ($asset->current_stage === 'analysis') {
            $asset->update(['current_stage' => 'review']);
            ActivityKanban::logAssetStageMove($asset, $request->user(), 'analysis', 'review');
        }

        ActivityKanban::create([
            'project_id' => $asset->project_id,
            'project_asset_id' => $asset->id,
            'user_id' => $request->user()->id,
            'activity_type' => 'comment',
            'stage_context' => 'analysis',
            'description' => "Kertas kerja untuk '{$asset->name}' selesai. Siap untuk review internal.",
        ]);

        return redirect()
            ->route('appraisal.assets.show', $asset)
            ->with('success', 'Kertas kerja selesai. Objek dipindahkan ke tahap review.');
    }

    /**
     * Remove the specified working paper
     */
    public function destroy(Request $request, WorkingPaperKanban $workingPaper)
    {
        $asset = $workingPaper->asset;
        $workingPaper->delete();

        ActivityKanban::create([
            'project_id' => $asset->project_id,
            'project_asset_id' => $asset->id,
            'user_id' => $request->user()->id,
            'activity_type' => 'comment',
            'stage_context' => $asset->current_stage,
            'description' => 'Kertas kerja dihapus.',
        ]);

        return redirect()
            ->route('appraisal.assets.show', $asset)
            ->with('success', 'Kertas kerja berhasil dihapus.');
    }
}

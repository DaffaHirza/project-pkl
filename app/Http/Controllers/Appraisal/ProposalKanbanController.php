<?php

namespace App\Http\Controllers\Appraisal;

use App\Http\Controllers\Controller;
use App\Models\ProjectKanban;
use App\Models\ProposalKanban;
use App\Models\ActivityKanban;
use Illuminate\Http\Request;

class ProposalKanbanController extends Controller
{
    /**
     * Display a listing of proposals
     */
    public function index(Request $request)
    {
        $query = ProposalKanban::with(['project.client']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('proposal_number', 'like', "%{$search}%")
                  ->orWhereHas('project', function ($q) use ($search) {
                      $q->where('name', 'like', "%{$search}%");
                  });
        }

        $proposals = $query->orderBy('created_at', 'desc')->paginate(20);

        return view('appraisal.proposals.index', compact('proposals'));
    }

    /**
     * Show the form for creating a new proposal
     */
    public function create(ProjectKanban $project)
    {
        return view('appraisal.proposals.create', compact('project'));
    }

    /**
     * Store a newly created proposal
     */
    public function store(Request $request, ProjectKanban $project)
    {
        $validated = $request->validate([
            'date_sent' => 'required|date',
            'status' => 'required|in:draft,sent,approved,rejected',
        ]);

        $validated['project_id'] = $project->id;
        $validated['proposal_number'] = ProposalKanban::generateProposalNumber();

        $proposal = ProposalKanban::create($validated);

        // Update project stage if needed
        if ($project->current_stage === 'lead') {
            $project->update(['current_stage' => 'proposal']);
            ActivityKanban::logStageMove($project, $request->user(), 'lead', 'proposal');
        }

        // Log activity
        ActivityKanban::create([
            'project_id' => $project->id,
            'user_id' => $request->user()->id,
            'activity_type' => 'upload',
            'stage_context' => 'proposal',
            'description' => "Proposal {$proposal->proposal_number} dibuat dengan status: " . ProposalKanban::STATUS[$validated['status']],
        ]);

        return redirect()
            ->route('appraisal.projects.show', $project)
            ->with('success', 'Proposal berhasil dibuat: ' . $proposal->proposal_number);
    }

    /**
     * Display the specified proposal
     */
    public function show(ProposalKanban $proposal)
    {
        $proposal->load(['project.client']);
        return view('appraisal.proposals.show', compact('proposal'));
    }

    /**
     * Update the specified proposal
     */
    public function update(Request $request, ProposalKanban $proposal)
    {
        $validated = $request->validate([
            'date_sent' => 'required|date',
            'status' => 'required|in:draft,sent,approved,rejected',
        ]);

        $oldStatus = $proposal->status;
        $proposal->update($validated);

        $project = $proposal->project;

        // If proposal approved, move to contract stage
        if ($validated['status'] === 'approved' && $oldStatus !== 'approved') {
            if ($project->current_stage === 'proposal') {
                $project->update(['current_stage' => 'contract']);
                ActivityKanban::logStageMove($project, $request->user(), 'proposal', 'contract');
            }

            ActivityKanban::create([
                'project_id' => $project->id,
                'user_id' => $request->user()->id,
                'activity_type' => 'approval',
                'stage_context' => 'proposal',
                'description' => "Proposal {$proposal->proposal_number} disetujui klien.",
            ]);
        }

        // If proposal rejected
        if ($validated['status'] === 'rejected' && $oldStatus !== 'rejected') {
            $project->update(['priority_status' => 'critical']);
            
            ActivityKanban::create([
                'project_id' => $project->id,
                'user_id' => $request->user()->id,
                'activity_type' => 'rejection',
                'stage_context' => 'proposal',
                'description' => "Proposal {$proposal->proposal_number} ditolak klien.",
            ]);
        }

        return redirect()
            ->route('appraisal.projects.show', $project)
            ->with('success', 'Proposal berhasil diperbarui.');
    }

    /**
     * Remove the specified proposal
     */
    public function destroy(Request $request, ProposalKanban $proposal)
    {
        $project = $proposal->project;
        $proposalNumber = $proposal->proposal_number;
        
        $proposal->delete();

        ActivityKanban::create([
            'project_id' => $project->id,
            'user_id' => $request->user()->id,
            'activity_type' => 'comment',
            'stage_context' => $project->current_stage,
            'description' => "Proposal {$proposalNumber} dihapus.",
        ]);

        return redirect()
            ->route('appraisal.projects.show', $project)
            ->with('success', 'Proposal berhasil dihapus.');
    }

    /**
     * Quick status update (AJAX)
     */
    public function updateStatus(Request $request, ProposalKanban $proposal)
    {
        $validated = $request->validate([
            'status' => 'required|in:draft,sent,approved,rejected',
        ]);

        $proposal->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Status proposal diperbarui.',
            'status' => $validated['status'],
            'status_label' => ProposalKanban::STATUS[$validated['status']],
        ]);
    }
}

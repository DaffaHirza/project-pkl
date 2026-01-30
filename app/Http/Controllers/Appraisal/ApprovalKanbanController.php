<?php

namespace App\Http\Controllers\Appraisal;

use App\Http\Controllers\Controller;
use App\Models\ProjectKanban;
use App\Models\ProjectAsset;
use App\Models\ApprovalKanban;
use App\Models\ActivityKanban;
use Illuminate\Http\Request;

class ApprovalKanbanController extends Controller
{
    /**
     * Display a listing of approvals
     */
    public function index(Request $request)
    {
        $query = ApprovalKanban::with(['project.client', 'asset', 'user']);

        if ($request->filled('stage')) {
            $query->where('stage', $request->stage);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('level')) {
            if ($request->level === 'project') {
                $query->projectLevel();
            } else {
                $query->assetLevel();
            }
        }

        $approvals = $query->orderBy('created_at', 'desc')->paginate(20);
        $projects = ProjectKanban::with('client')->ongoing()->orderBy('name')->get();

        return view('appraisal.approvals.index', compact('approvals', 'projects'));
    }

    // ==========================================
    // PROJECT LEVEL APPROVALS (ADMIN WORKFLOW)
    // ==========================================

    /**
     * Store proposal approval (lead → proposal → contract)
     */
    public function storeProposalApproval(Request $request, ProjectKanban $project)
    {
        $validated = $request->validate([
            'status' => 'required|in:approved,rejected',
            'comments' => 'nullable|string',
        ]);

        $approval = ApprovalKanban::create([
            'project_id' => $project->id,
            'project_asset_id' => null,
            'user_id' => $request->user()->id,
            'approval_level' => 'project',
            'stage' => 'proposal_approval',
            'status' => $validated['status'],
            'comments' => $validated['comments'],
        ]);

        ActivityKanban::logApproval(
            $project,
            $request->user(),
            'proposal_approval',
            $validated['status'] === 'approved',
            $validated['comments']
        );

        if ($validated['status'] === 'approved') {
            if ($project->current_stage === 'proposal') {
                $project->update(['current_stage' => 'contract']);
                ActivityKanban::logStageMove($project, $request->user(), 'proposal', 'contract');
            }

            return redirect()
                ->route('appraisal.projects.show', $project)
                ->with('success', 'Proposal disetujui. Silakan proses kontrak.');
        } else {
            $project->update(['priority_status' => 'warning']);

            return redirect()
                ->route('appraisal.projects.show', $project)
                ->with('warning', 'Proposal ditolak. Diperlukan revisi.');
        }
    }

    /**
     * Store contract approval (contract → invoicing when all assets done)
     */
    public function storeContractApproval(Request $request, ProjectKanban $project)
    {
        $validated = $request->validate([
            'comments' => 'nullable|string',
        ]);

        $project->update([
            'current_stage' => 'contract',
        ]);

        ApprovalKanban::create([
            'project_id' => $project->id,
            'project_asset_id' => null,
            'user_id' => $request->user()->id,
            'approval_level' => 'project',
            'stage' => 'contract_signed',
            'status' => 'approved',
            'comments' => $validated['comments'],
        ]);

        ActivityKanban::create([
            'project_id' => $project->id,
            'user_id' => $request->user()->id,
            'activity_type' => 'approval',
            'stage_context' => 'contract',
            'description' => 'Kontrak ditandatangani.',
        ]);

        return redirect()
            ->route('appraisal.projects.show', $project)
            ->with('success', 'Kontrak berhasil dicatat. Silakan tambahkan aset/objek penilaian.');
    }

    /**
     * Store invoice approval (invoicing → done)
     */
    public function storeInvoiceApproval(Request $request, ProjectKanban $project)
    {
        $validated = $request->validate([
            'status' => 'required|in:approved,rejected',
            'comments' => 'nullable|string',
        ]);

        $approval = ApprovalKanban::create([
            'project_id' => $project->id,
            'project_asset_id' => null,
            'user_id' => $request->user()->id,
            'approval_level' => 'project',
            'stage' => 'invoice_paid',
            'status' => $validated['status'],
            'comments' => $validated['comments'],
        ]);

        if ($validated['status'] === 'approved') {
            $project->update([
                'current_stage' => 'done',
            ]);

            ActivityKanban::create([
                'project_id' => $project->id,
                'user_id' => $request->user()->id,
                'activity_type' => 'approval',
                'stage_context' => 'invoicing',
                'description' => 'Invoice telah dibayar. Project selesai.',
            ]);

            return redirect()
                ->route('appraisal.projects.show', $project)
                ->with('success', 'Invoice disetujui. Project selesai.');
        } else {
            ActivityKanban::create([
                'project_id' => $project->id,
                'user_id' => $request->user()->id,
                'activity_type' => 'rejection',
                'stage_context' => 'invoicing',
                'description' => 'Invoice ditolak. Catatan: ' . ($validated['comments'] ?? '-'),
            ]);

            return redirect()
                ->route('appraisal.projects.show', $project)
                ->with('warning', 'Invoice ditolak.');
        }
    }

    // ==========================================
    // ASSET LEVEL APPROVALS (TECHNICAL WORKFLOW)
    // ==========================================

    /**
     * Store internal review approval for asset
     */
    public function storeAssetInternalReview(Request $request, ProjectAsset $asset)
    {
        $validated = $request->validate([
            'status' => 'required|in:approved,rejected',
            'comments' => 'nullable|string',
        ]);

        $approval = ApprovalKanban::create([
            'project_id' => $asset->project_id,
            'project_asset_id' => $asset->id,
            'user_id' => $request->user()->id,
            'approval_level' => 'asset',
            'stage' => 'internal_review',
            'status' => $validated['status'],
            'comments' => $validated['comments'],
        ]);

        ActivityKanban::create([
            'project_id' => $asset->project_id,
            'project_asset_id' => $asset->id,
            'user_id' => $request->user()->id,
            'activity_type' => $validated['status'] === 'approved' ? 'approval' : 'rejection',
            'stage_context' => $asset->current_stage,
            'description' => $validated['status'] === 'approved'
                ? "Review internal untuk '{$asset->name}' disetujui."
                : "Review internal untuk '{$asset->name}' ditolak. Catatan: " . ($validated['comments'] ?? '-'),
        ]);

        if ($validated['status'] === 'approved') {
            if ($asset->current_stage === 'review') {
                $asset->update(['current_stage' => 'client_approval']);
                ActivityKanban::logAssetStageMove($asset, $request->user(), 'review', 'client_approval');
            }

            return redirect()
                ->route('appraisal.assets.show', $asset)
                ->with('success', 'Review internal disetujui. Aset siap untuk approval klien.');
        } else {
            $asset->update(['priority_status' => 'warning']);

            return redirect()
                ->route('appraisal.assets.show', $asset)
                ->with('warning', 'Review internal ditolak. Diperlukan revisi.');
        }
    }

    /**
     * Store client approval for asset
     */
    public function storeAssetClientApproval(Request $request, ProjectAsset $asset)
    {
        $validated = $request->validate([
            'status' => 'required|in:approved,rejected',
            'comments' => 'nullable|string',
        ]);

        $approval = ApprovalKanban::create([
            'project_id' => $asset->project_id,
            'project_asset_id' => $asset->id,
            'user_id' => null, // Client approval
            'approval_level' => 'asset',
            'stage' => 'client_approval',
            'status' => $validated['status'],
            'comments' => $validated['comments'],
        ]);

        ActivityKanban::create([
            'project_id' => $asset->project_id,
            'project_asset_id' => $asset->id,
            'user_id' => $request->user()->id,
            'activity_type' => $validated['status'] === 'approved' ? 'approval' : 'rejection',
            'stage_context' => 'client_approval',
            'description' => $validated['status'] === 'approved'
                ? "Klien menyetujui draft laporan untuk '{$asset->name}'."
                : "Klien menolak draft untuk '{$asset->name}'. Catatan: " . ($validated['comments'] ?? '-'),
        ]);

        if ($validated['status'] === 'approved') {
            if ($asset->current_stage === 'client_approval') {
                $asset->update([
                    'current_stage' => 'final_report',
                    'priority_status' => 'normal',
                ]);
                ActivityKanban::logAssetStageMove($asset, $request->user(), 'client_approval', 'final_report');
            }

            return redirect()
                ->route('appraisal.assets.show', $asset)
                ->with('success', 'Approval klien diterima. Silakan cetak laporan final.');
        } else {
            $asset->update([
                'priority_status' => 'critical',
                'current_stage' => 'review',
            ]);

            return redirect()
                ->route('appraisal.assets.show', $asset)
                ->with('warning', 'Klien menolak draft. Aset dikembalikan ke tahap review.');
        }
    }

    /**
     * Display the specified approval
     */
    public function show(ApprovalKanban $approval)
    {
        $approval->load(['project.client', 'asset', 'user']);
        return view('appraisal.approvals.show', compact('approval'));
    }

    /**
     * Get pending approvals count (for notifications)
     */
    public function pendingCount()
    {
        // Project level pending
        $pendingProposals = ProjectKanban::where('current_stage', 'proposal')->count();
        $pendingInvoices = ProjectKanban::where('current_stage', 'invoicing')->count();
        
        // Asset level pending
        $pendingAssetReviews = ProjectAsset::where('current_stage', 'review')->count();
        $pendingAssetClientApprovals = ProjectAsset::where('current_stage', 'client_approval')->count();

        return response()->json([
            'project_level' => [
                'pending_proposals' => $pendingProposals,
                'pending_invoices' => $pendingInvoices,
            ],
            'asset_level' => [
                'pending_reviews' => $pendingAssetReviews,
                'pending_client_approvals' => $pendingAssetClientApprovals,
            ],
            'total' => $pendingProposals + $pendingInvoices + $pendingAssetReviews + $pendingAssetClientApprovals,
        ]);
    }
}

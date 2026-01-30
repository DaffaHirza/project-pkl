<?php

namespace App\Http\Controllers\Appraisal;

use App\Http\Controllers\Controller;
use App\Models\ProjectKanban;
use App\Models\ContractKanban;
use App\Models\ActivityKanban;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ContractKanbanController extends Controller
{
    /**
     * Display a listing of contracts
     */
    public function index(Request $request)
    {
        $query = ContractKanban::with(['project.client']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('spk_number', 'like', "%{$search}%")
                  ->orWhereHas('project', function ($q) use ($search) {
                      $q->where('name', 'like', "%{$search}%");
                  });
        }

        $contracts = $query->orderBy('signed_date', 'desc')->paginate(20);

        return view('appraisal.contracts.index', compact('contracts'));
    }

    /**
     * Show the form for creating a new contract
     */
    public function create(ProjectKanban $project)
    {
        return view('appraisal.contracts.create', compact('project'));
    }

    /**
     * Store a newly created contract
     */
    public function store(Request $request, ProjectKanban $project)
    {
        $validated = $request->validate([
            'spk_number' => 'nullable|string|max:100',
            'signed_date' => 'required|date',
            'contract_file' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:10240',
        ]);

        $filePath = null;
        if ($request->hasFile('contract_file')) {
            $filePath = $request->file('contract_file')->store('contracts', 'public');
        }

        // Generate SPK number if not provided
        if (empty($validated['spk_number'])) {
            $validated['spk_number'] = ContractKanban::generateSpkNumber();
        }

        $contract = ContractKanban::create([
            'project_id' => $project->id,
            'spk_number' => $validated['spk_number'],
            'signed_date' => $validated['signed_date'],
            'file_path' => $filePath,
        ]);

        // Update project stage
        if (in_array($project->current_stage, ['lead', 'proposal', 'contract'])) {
            $oldStage = $project->current_stage;
            $project->update(['current_stage' => 'inspection']);
            ActivityKanban::logStageMove($project, $request->user(), $oldStage, 'inspection');
        }

        // Log activity
        ActivityKanban::create([
            'project_id' => $project->id,
            'user_id' => $request->user()->id,
            'activity_type' => 'upload',
            'stage_context' => 'contract',
            'description' => "Kontrak {$contract->spk_number} ditambahkan. Siap untuk inspeksi.",
        ]);

        return redirect()
            ->route('appraisal.projects.show', $project)
            ->with('success', 'Kontrak berhasil ditambahkan.');
    }

    /**
     * Display the specified contract
     */
    public function show(ContractKanban $contract)
    {
        $contract->load(['project.client']);
        return view('appraisal.contracts.show', compact('contract'));
    }

    /**
     * Update the specified contract
     */
    public function update(Request $request, ContractKanban $contract)
    {
        $validated = $request->validate([
            'spk_number' => 'nullable|string|max:100',
            'signed_date' => 'required|date',
            'contract_file' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:10240',
        ]);

        $data = [
            'spk_number' => $validated['spk_number'],
            'signed_date' => $validated['signed_date'],
        ];

        if ($request->hasFile('contract_file')) {
            // Delete old file
            if ($contract->file_path) {
                Storage::disk('public')->delete($contract->file_path);
            }
            $data['file_path'] = $request->file('contract_file')->store('contracts', 'public');
        }

        $contract->update($data);

        return redirect()
            ->route('appraisal.projects.show', $contract->project)
            ->with('success', 'Kontrak berhasil diperbarui.');
    }

    /**
     * Remove the specified contract
     */
    public function destroy(Request $request, ContractKanban $contract)
    {
        $project = $contract->project;
        
        // Delete file if exists
        if ($contract->file_path) {
            Storage::disk('public')->delete($contract->file_path);
        }
        
        $spkNumber = $contract->spk_number;
        $contract->delete();

        ActivityKanban::create([
            'project_id' => $project->id,
            'user_id' => $request->user()->id,
            'activity_type' => 'comment',
            'stage_context' => $project->current_stage,
            'description' => "Kontrak {$spkNumber} dihapus.",
        ]);

        return redirect()
            ->route('appraisal.projects.show', $project)
            ->with('success', 'Kontrak berhasil dihapus.');
    }

    /**
     * Download contract file
     */
    public function download(ContractKanban $contract)
    {
        if (!$contract->file_path || !Storage::disk('public')->exists($contract->file_path)) {
            return back()->with('error', 'File kontrak tidak ditemukan.');
        }

        return response()->download(
            Storage::disk('public')->path($contract->file_path),
            'Kontrak_' . $contract->spk_number . '.' . pathinfo($contract->file_path, PATHINFO_EXTENSION)
        );
    }
}

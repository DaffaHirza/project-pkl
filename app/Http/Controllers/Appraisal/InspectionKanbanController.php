<?php

namespace App\Http\Controllers\Appraisal;

use App\Http\Controllers\Controller;
use App\Models\ProjectKanban;
use App\Models\ProjectAsset;
use App\Models\InspectionKanban;
use App\Models\ActivityKanban;
use App\Models\User;
use Illuminate\Http\Request;

class InspectionKanbanController extends Controller
{
    /**
     * Display a listing of inspections
     */
    public function index(Request $request)
    {
        $query = InspectionKanban::with(['asset.project.client', 'surveyor']);

        if ($request->filled('surveyor_id')) {
            $query->where('surveyor_id', $request->surveyor_id);
        }

        if ($request->filled('project_id')) {
            $query->whereHas('asset', fn($q) => $q->where('project_id', $request->project_id));
        }

        if ($request->filled('asset_id')) {
            $query->where('project_asset_id', $request->asset_id);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('inspection_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('inspection_date', '<=', $request->date_to);
        }

        $inspections = $query->orderBy('inspection_date', 'desc')->paginate(20);
        $surveyors = User::orderBy('name')->get();
        $projects = ProjectKanban::with('client')->ongoing()->orderBy('name')->get();

        return view('appraisal.inspections.index', compact('inspections', 'surveyors', 'projects'));
    }

    /**
     * Show the form for creating a new inspection (now for an ASSET)
     */
    public function create(ProjectAsset $asset)
    {
        $asset->load('project.client');
        $users = User::orderBy('name')->get();
        return view('appraisal.inspections.create', compact('asset', 'users'));
    }

    /**
     * Store a newly created inspection (now linked to ASSET)
     */
    public function store(Request $request, ProjectAsset $asset)
    {
        $validated = $request->validate([
            'surveyor_id' => 'required|exists:users,id',
            'inspection_date' => 'required|date',
            'notes' => 'nullable|string',
            'latitude' => 'nullable|string|max:50',
            'longitude' => 'nullable|string|max:50',
        ]);

        $validated['project_asset_id'] = $asset->id;
        // Keep project_id for backward compatibility
        $validated['project_id'] = $asset->project_id;

        $inspection = InspectionKanban::create($validated);

        // Log activity
        $surveyor = User::find($validated['surveyor_id']);
        ActivityKanban::create([
            'project_id' => $asset->project_id,
            'project_asset_id' => $asset->id,
            'user_id' => $request->user()->id,
            'activity_type' => 'comment',
            'stage_context' => 'inspection',
            'description' => "Inspeksi untuk '{$asset->name}' dijadwalkan pada " . $inspection->inspection_date->format('d/m/Y') . " oleh {$surveyor->name}.",
        ]);

        return redirect()
            ->route('appraisal.assets.show', $asset)
            ->with('success', 'Jadwal inspeksi berhasil ditambahkan.');
    }

    /**
     * Display the specified inspection
     */
    public function show(InspectionKanban $inspection)
    {
        $inspection->load(['asset.project.client', 'surveyor', 'asset.documents' => function ($q) {
            $q->where('category', 'field_photo');
        }]);
        
        return view('appraisal.inspections.show', compact('inspection'));
    }

    /**
     * Update the specified inspection
     */
    public function update(Request $request, InspectionKanban $inspection)
    {
        $validated = $request->validate([
            'surveyor_id' => 'required|exists:users,id',
            'inspection_date' => 'required|date',
            'notes' => 'nullable|string',
            'latitude' => 'nullable|string|max:50',
            'longitude' => 'nullable|string|max:50',
        ]);

        $inspection->update($validated);

        return redirect()
            ->route('appraisal.assets.show', $inspection->asset)
            ->with('success', 'Data inspeksi berhasil diperbarui.');
    }

    /**
     * Mark inspection as completed and move asset to analysis stage
     */
    public function complete(Request $request, InspectionKanban $inspection)
    {
        $validated = $request->validate([
            'notes' => 'nullable|string',
        ]);

        if ($validated['notes']) {
            $inspection->update(['notes' => $validated['notes']]);
        }

        $asset = $inspection->asset;

        // Move asset to analysis stage (not project)
        if ($asset->current_stage === 'inspection') {
            $asset->update(['current_stage' => 'analysis']);
            ActivityKanban::logAssetStageMove($asset, $request->user(), 'inspection', 'analysis');
        }

        ActivityKanban::create([
            'project_id' => $asset->project_id,
            'project_asset_id' => $asset->id,
            'user_id' => $request->user()->id,
            'activity_type' => 'comment',
            'stage_context' => 'inspection',
            'description' => "Inspeksi untuk '{$asset->name}' selesai. Data siap untuk dianalisis.",
        ]);

        return redirect()
            ->route('appraisal.assets.show', $asset)
            ->with('success', 'Inspeksi selesai. Objek dipindahkan ke tahap analisis.');
    }

    /**
     * Remove the specified inspection
     */
    public function destroy(Request $request, InspectionKanban $inspection)
    {
        $asset = $inspection->asset;
        $inspection->delete();

        ActivityKanban::create([
            'project_id' => $asset->project_id,
            'project_asset_id' => $asset->id,
            'user_id' => $request->user()->id,
            'activity_type' => 'comment',
            'stage_context' => $asset->current_stage,
            'description' => 'Data inspeksi dihapus.',
        ]);

        return redirect()
            ->route('appraisal.assets.show', $asset)
            ->with('success', 'Data inspeksi berhasil dihapus.');
    }

    /**
     * Update GPS coordinates (from mobile)
     */
    public function updateLocation(Request $request, InspectionKanban $inspection)
    {
        $validated = $request->validate([
            'latitude' => 'required|string|max:50',
            'longitude' => 'required|string|max:50',
        ]);

        $inspection->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Lokasi GPS diperbarui.',
            'coordinates' => $inspection->coordinates,
        ]);
    }

    /**
     * Get today's inspections for dashboard
     */
    public function today()
    {
        $inspections = InspectionKanban::with(['asset.project.client', 'surveyor'])
            ->whereDate('inspection_date', today())
            ->orderBy('inspection_date')
            ->get();

        return response()->json($inspections);
    }
}

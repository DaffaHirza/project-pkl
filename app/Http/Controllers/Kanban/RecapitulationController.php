<?php

namespace App\Http\Controllers\Kanban;

use App\Http\Controllers\Controller;
use App\Models\AssetKanban;
use App\Models\AssetNoteKanban;
use App\Models\RecapitulationKanban;
use App\Models\RecapitulationItemKanban;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class RecapitulationController extends Controller
{
    /**
     * Display list of all recapitulations
     */
    public function index(Request $request)
    {
        $query = RecapitulationKanban::query()
            ->with('creator:id,name')
            ->withCount('items');

        // Filter by status
        if ($request->filled('status') && in_array($request->status, ['draft', 'published'])) {
            $query->where('status', $request->status);
        }

        // Filter by year
        if ($request->filled('year')) {
            $query->whereYear('period_start', $request->year);
        }

        $recapitulations = $query->latest('period_end')->paginate(15)->withQueryString();

        // Get available years for filter
        $years = RecapitulationKanban::selectRaw('EXTRACT(YEAR FROM period_start) as year')
            ->distinct()
            ->orderByDesc('year')
            ->pluck('year');

        return view('kanban.recapitulations.index', compact('recapitulations', 'years'));
    }

    /**
     * Show form to create new recapitulation
     */
    public function create()
    {
        $suggestedPeriod = RecapitulationKanban::getSuggestedPeriod();
        $suggestedTitle = RecapitulationKanban::generateTitle(
            $suggestedPeriod['start'], 
            $suggestedPeriod['end']
        );

        return view('kanban.recapitulations.create', compact('suggestedPeriod', 'suggestedTitle'));
    }

    /**
     * Store new recapitulation
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'period_start' => 'required|date',
            'period_end' => 'required|date|after_or_equal:period_start',
            'summary' => 'nullable|string|max:2000',
            'auto_generate' => 'boolean',
        ], [
            'title.required' => 'Judul rekapitulasi wajib diisi.',
            'period_start.required' => 'Tanggal mulai wajib diisi.',
            'period_end.required' => 'Tanggal akhir wajib diisi.',
            'period_end.after_or_equal' => 'Tanggal akhir harus setelah atau sama dengan tanggal mulai.',
        ]);

        DB::beginTransaction();
        try {
            $recapitulation = RecapitulationKanban::create([
                'title' => strip_tags(trim($validated['title'])),
                'period_start' => $validated['period_start'],
                'period_end' => $validated['period_end'],
                'summary' => $validated['summary'] ? strip_tags($validated['summary']) : null,
                'created_by' => Auth::id(),
            ]);

            // Auto-generate items if requested
            if ($request->boolean('auto_generate')) {
                $this->generateItems($recapitulation);
            }

            DB::commit();

            return redirect()
                ->route('kanban.recapitulations.show', $recapitulation)
                ->with('success', 'Rekapitulasi berhasil dibuat.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Gagal membuat rekapitulasi: ' . $e->getMessage());
        }
    }

    /**
     * Display recapitulation detail
     */
    public function show(RecapitulationKanban $recapitulation)
    {
        $recapitulation->load([
            'creator:id,name',
            'items' => fn($q) => $q->with([
                'asset:id,name,asset_type,current_stage,client_id',
                'asset.client:id,name,company_name'
            ])->orderBy('work_status')->orderByDesc('stage_end')
        ]);

        $summary = $recapitulation->progress_summary;
        $stages = AssetKanban::STAGES;

        return view('kanban.recapitulations.show', compact('recapitulation', 'summary', 'stages'));
    }

    /**
     * Show edit form
     */
    public function edit(RecapitulationKanban $recapitulation)
    {
        if ($recapitulation->isPublished()) {
            return back()->with('error', 'Rekapitulasi yang sudah dipublikasikan tidak bisa diedit.');
        }

        $recapitulation->load(['items.asset:id,name,current_stage']);

        return view('kanban.recapitulations.edit', compact('recapitulation'));
    }

    /**
     * Update recapitulation
     */
    public function update(Request $request, RecapitulationKanban $recapitulation)
    {
        if ($recapitulation->isPublished()) {
            return back()->with('error', 'Rekapitulasi yang sudah dipublikasikan tidak bisa diedit.');
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'period_start' => 'required|date',
            'period_end' => 'required|date|after_or_equal:period_start',
            'summary' => 'nullable|string|max:2000',
        ]);

        $recapitulation->update([
            'title' => strip_tags(trim($validated['title'])),
            'period_start' => $validated['period_start'],
            'period_end' => $validated['period_end'],
            'summary' => $validated['summary'] ? strip_tags($validated['summary']) : null,
        ]);

        return redirect()
            ->route('kanban.recapitulations.show', $recapitulation)
            ->with('success', 'Rekapitulasi berhasil diupdate.');
    }

    /**
     * Delete recapitulation
     */
    public function destroy(RecapitulationKanban $recapitulation)
    {
        $title = $recapitulation->title;
        $recapitulation->delete();

        return redirect()
            ->route('kanban.recapitulations.index')
            ->with('success', "Rekapitulasi '{$title}' berhasil dihapus.");
    }

    /**
     * Publish recapitulation
     */
    public function publish(RecapitulationKanban $recapitulation)
    {
        if ($recapitulation->items->isEmpty()) {
            return back()->with('error', 'Tidak bisa mempublikasikan rekapitulasi tanpa item.');
        }

        $recapitulation->publish();

        return back()->with('success', 'Rekapitulasi berhasil dipublikasikan.');
    }

    /**
     * Unpublish recapitulation
     */
    public function unpublish(RecapitulationKanban $recapitulation)
    {
        $recapitulation->unpublish();

        return back()->with('success', 'Rekapitulasi dikembalikan ke draft.');
    }

    /**
     * Regenerate items from activity logs
     */
    public function regenerate(RecapitulationKanban $recapitulation)
    {
        if ($recapitulation->isPublished()) {
            return back()->with('error', 'Rekapitulasi yang sudah dipublikasikan tidak bisa di-regenerate.');
        }

        DB::beginTransaction();
        try {
            // Delete existing items
            $recapitulation->items()->delete();
            
            // Generate new items
            $count = $this->generateItems($recapitulation);
            
            DB::commit();

            return back()->with('success', "{$count} item berhasil di-generate ulang.");

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal regenerate items: ' . $e->getMessage());
        }
    }

    /**
     * Update single item
     */
    public function updateItem(Request $request, RecapitulationItemKanban $item)
    {
        if ($item->recapitulation->isPublished()) {
            return response()->json(['success' => false, 'message' => 'Tidak bisa edit item yang sudah dipublikasikan.'], 403);
        }

        $validated = $request->validate([
            'work_status' => 'required|in:not_started,in_progress,completed,blocked,pending_review',
            'activities' => 'nullable|string|max:2000',
            'notes' => 'nullable|string|max:1000',
            'next_actions' => 'nullable|string|max:1000',
        ]);

        $item->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Item berhasil diupdate.',
            'item' => $item->fresh(),
        ]);
    }

    /**
     * Add asset to recapitulation
     */
    public function addItem(Request $request, RecapitulationKanban $recapitulation)
    {
        if ($recapitulation->isPublished()) {
            return response()->json(['success' => false, 'message' => 'Tidak bisa tambah item ke rekapitulasi yang sudah dipublikasikan.'], 403);
        }

        $validated = $request->validate([
            'asset_id' => 'required|exists:assets_kanban,id',
        ]);

        // Check if already exists
        if ($recapitulation->items()->where('asset_id', $validated['asset_id'])->exists()) {
            return response()->json(['success' => false, 'message' => 'Asset sudah ada dalam rekapitulasi.'], 400);
        }

        $asset = AssetKanban::find($validated['asset_id']);
        
        // Get stage at period start (from notes)
        $stageAtStart = $this->getAssetStageAtDate($asset, $recapitulation->period_start);

        $item = $recapitulation->items()->create([
            'asset_id' => $asset->id,
            'stage_start' => $stageAtStart,
            'stage_end' => $asset->current_stage,
            'work_status' => 'in_progress',
        ]);

        // Auto-generate activities
        $item->update([
            'activities' => $item->generateActivitiesFromNotes(),
            'work_status' => $item->determineWorkStatus(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Asset berhasil ditambahkan.',
            'item' => $item->load('asset'),
        ]);
    }

    /**
     * Remove item from recapitulation
     */
    public function removeItem(RecapitulationItemKanban $item)
    {
        if ($item->recapitulation->isPublished()) {
            return response()->json(['success' => false, 'message' => 'Tidak bisa hapus item dari rekapitulasi yang sudah dipublikasikan.'], 403);
        }

        $item->delete();

        return response()->json(['success' => true, 'message' => 'Item berhasil dihapus.']);
    }

    /**
     * Print-friendly view
     */
    public function print(RecapitulationKanban $recapitulation)
    {
        $recapitulation->load([
            'creator:id,name',
            'items' => fn($q) => $q->with([
                'asset:id,name,asset_type,current_stage,client_id',
                'asset.client:id,name,company_name'
            ])->orderBy('work_status')
        ]);

        $summary = $recapitulation->progress_summary;
        $stages = AssetKanban::STAGES;

        return view('kanban.recapitulations.print', compact('recapitulation', 'summary', 'stages'));
    }

    /**
     * Get assets available to add (not yet in recapitulation)
     */
    public function availableAssets(RecapitulationKanban $recapitulation)
    {
        $existingAssetIds = $recapitulation->items()->pluck('asset_id');

        $assets = AssetKanban::query()
            ->select('id', 'name', 'asset_type', 'current_stage', 'client_id')
            ->with('client:id,name,company_name')
            ->whereNotIn('id', $existingAssetIds)
            ->where('current_stage', '<', 13) // Exclude completed
            ->orderBy('name')
            ->get();

        return response()->json($assets);
    }

    // ==========================================
    // PRIVATE METHODS
    // ==========================================

    /**
     * Generate items from active assets with activity in period
     */
    private function generateItems(RecapitulationKanban $recapitulation): int
    {
        $periodStart = $recapitulation->period_start;
        $periodEnd = $recapitulation->period_end;

        // Get assets that have activity in this period
        $assetIdsWithActivity = AssetNoteKanban::whereBetween('created_at', [
            $periodStart->startOfDay(),
            $periodEnd->endOfDay()
        ])->distinct()->pluck('asset_id');

        // Also include assets that are currently active (not completed)
        $activeAssetIds = AssetKanban::where('current_stage', '<', 13)->pluck('id');

        // Merge and unique
        $assetIds = $assetIdsWithActivity->merge($activeAssetIds)->unique();

        $count = 0;
        foreach ($assetIds as $assetId) {
            $asset = AssetKanban::find($assetId);
            if (!$asset) continue;

            // Get stage at period start
            $stageAtStart = $this->getAssetStageAtDate($asset, $periodStart);

            $item = $recapitulation->items()->create([
                'asset_id' => $asset->id,
                'stage_start' => $stageAtStart,
                'stage_end' => $asset->current_stage,
            ]);

            // Auto-generate activities and determine status
            $item->update([
                'activities' => $item->generateActivitiesFromNotes(),
                'work_status' => $item->determineWorkStatus(),
            ]);

            $count++;
        }

        return $count;
    }

    /**
     * Get asset stage at a specific date by checking notes history
     */
    private function getAssetStageAtDate(AssetKanban $asset, Carbon $date): int
    {
        // Find the last stage_change note before or at the date
        $lastStageNote = $asset->notes()
            ->where('type', 'stage_change')
            ->where('created_at', '<=', $date->endOfDay())
            ->orderByDesc('created_at')
            ->first();

        if ($lastStageNote) {
            return $lastStageNote->stage;
        }

        // If no note found, assume it was at stage 1
        return 1;
    }
}

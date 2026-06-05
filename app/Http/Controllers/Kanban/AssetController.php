<?php

namespace App\Http\Controllers\Kanban;

use App\Http\Controllers\Controller;
use App\Models\ClientKanban;
use App\Models\AssetKanban;
use App\Services\KanbanNotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Services\GoogleDriveService;
use Illuminate\Support\Facades\Log;
use App\Models\AssetStageCheckKanban;
use Illuminate\Validation\Rule;

class AssetController extends Controller
{
    /**
     * Display kanban board with drag-and-drop
     */
    public function index(Request $request)
    {
        $query = AssetKanban::query()
            ->select('id', 'client_id', 'name', 'asset_type', 'current_stage', 'position', 'updated_at')
            ->with([
                'client:id,name,company_name,type,parent_id',
                'client.parent:id,name,company_name,type',
            ])
            ->withCount([
                'notes as warning_notes_count' => function ($q) {
                    $q->whereIn('type', ['rejection', 'blocked']);
                },
                'stageChecks as checked_stages_count' => function ($q) {
                    $q->where('is_checked', true);
                },
            ]);

        // Filter kategori klien
        if ($request->filled('client_category')) {
            match ($request->client_category) {
                'bank' => $query->whereHas(
                    'client',
                    fn($q) =>
                    $q->where('type', 'bank')
                ),
                'pt_cv_induk' => $query->whereHas(
                    'client',
                    fn($q) =>
                    $q->where('type', 'pt_cv')->whereNull('parent_id')
                ),
                'debitur' => $query->whereHas(
                    'client',
                    fn($q) =>
                    $q->where('type', 'debitur')
                ),
                'pt_cv_anak' => $query->whereHas(
                    'client',
                    fn($q) =>
                    $q->where('type', 'pt_cv')->whereNotNull('parent_id')
                ),
                default => null,
            };
        }

        // Filter klien spesifik
        if ($request->filled('client_id')) {
            $query->where('client_id', (int) $request->client_id);
        }

        // Filter tipe asset
        if ($request->filled('asset_type')) {
            $query->where('asset_type', $request->asset_type);
        }

        $assets = $query
            ->orderBy('position')
            ->orderByDesc('updated_at')
            ->get();

        $assetsByStage = [];

        foreach (AssetKanban::STAGES as $stageNum => $stageName) {
            $assetsByStage[$stageNum] = $assets
                ->where('current_stage', $stageNum)
                ->values();
        }

        $stages = AssetKanban::STAGES;
        $assetTypes = AssetKanban::ASSET_TYPES;

        $clients = ClientKanban::query()
            ->select('id', 'name', 'company_name', 'type', 'parent_id')
            ->orderBy('type')
            ->orderBy('name')
            ->get();

        $clientGroups = $this->getClientGroups($clients);

        return view('kanban.assets.index', compact(
            'assetsByStage',
            'stages',
            'clients',
            'clientGroups',
            'assetTypes'
        ));
    }

    private function getClientGroups($clients): array
    {
        return [
            'bank' => $clients
                ->where('type', 'bank')
                ->values(),

            'pt_cv_induk' => $clients
                ->where('type', 'pt_cv')
                ->whereNull('parent_id')
                ->values(),

            'debitur' => $clients
                ->where('type', 'debitur')
                ->values(),

            'pt_cv_anak' => $clients
                ->where('type', 'pt_cv')
                ->whereNotNull('parent_id')
                ->values(),
        ];
    }

    /**
     * Show create form with clients dropdown
     */
    public function create(Request $request)
    {
        $clients = ClientKanban::query()
            ->select('id', 'name', 'company_name', 'type', 'parent_id')
            ->orderBy('type')
            ->orderBy('name')
            ->get();

        $clientGroups = $this->getClientGroups($clients);
        $selectedClientId = $request->get('client_id');
        $assetTypes = AssetKanban::ASSET_TYPES;

        return view('kanban.assets.create', compact(
            'clients',
            'clientGroups',
            'selectedClientId',
            'assetTypes'
        ));
    }

    /**
     * Store new asset with validation, logging & notification
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'client_id' => 'required|exists:clients_kanban,id',
            'name' => 'required|string|max:255|min:3',
            'asset_type' => [
                'required',
                Rule::in(array_keys(AssetKanban::ASSET_TYPES)),
            ],
            'location' => 'nullable|string|max:500',
            'current_stage' => 'nullable|integer|min:1|max:13',
            'notes' => 'nullable|string|max:1000',
        ], [
            'client_id.required' => 'Klien wajib dipilih.',
            'name.required' => 'Nama asset wajib diisi.',
            'name.min' => 'Nama asset minimal 3 karakter.',
            'asset_type.required' => 'Tipe asset wajib dipilih.',
            'asset_type.in' => 'Tipe asset tidak valid.',
        ]);

        $validated['name'] = strip_tags(trim($validated['name']));
        $validated['location'] = !empty($validated['location'])
            ? strip_tags(trim($validated['location']))
            : null;

        $initialNote = !empty($validated['notes'])
            ? strip_tags(trim($validated['notes']))
            : null;

        unset($validated['notes']);

        $validated['current_stage'] = $validated['current_stage'] ?? 1;

        DB::beginTransaction();

        try {
            $asset = AssetKanban::create($validated);

            $asset->notes()->create([
                'user_id' => Auth::id(),
                'stage' => $asset->current_stage,
                'type' => 'stage_change',
                'content' => 'Asset dibuat dan memulai tahap ' . AssetKanban::STAGES[$asset->current_stage],
            ]);

            if ($initialNote) {
                $asset->notes()->create([
                    'user_id' => Auth::id(),
                    'stage' => $asset->current_stage,
                    'type' => 'note',
                    'content' => $initialNote,
                ]);
            }

            DB::commit();

            KanbanNotificationService::notifyAssetCreated($asset, Auth::user());

            return redirect()
                ->route('kanban.assets.show', $asset)
                ->with('success', 'Asset berhasil ditambahkan.');
        } catch (\Throwable $e) {
            DB::rollBack();

            return back()
                ->withInput()
                ->with('error', 'Gagal menambahkan asset: ' . $e->getMessage());
        }
    }

    /**
     * Display asset detail with documents & notes grouped by stage
     */
    // public function show(AssetKanban $asset)
    // {
    //     $asset->load([
    //         'client:id,name,company_name,type,parent_id',
    //         'client.parent:id,name,company_name',
    //         'documents' => fn($q) => $q->select('id', 'asset_id', 'uploaded_by', 'stage', 'file_name', 'file_path', 'file_type', 'file_size', 'created_at')
    //                                    ->with('uploader:id,name')
    //                                    ->orderBy('created_at', 'desc'),
    //         'notes' => fn($q) => $q->select('id', 'asset_id', 'user_id', 'stage', 'type', 'content', 'created_at')
    //                                ->with('user:id,name')
    //                                ->orderBy('created_at', 'desc')
    //     ]);

    //     $stages = AssetKanban::STAGES;

    //     // Group by stage efficiently (data already loaded)
    //     $documentsByStage = collect($stages)->mapWithKeys(fn($label, $num) => [
    //         $num => $asset->documents->where('stage', $num)->values()
    //     ]);

    //     $notesByStage = collect($stages)->mapWithKeys(fn($label, $num) => [
    //         $num => $asset->notes->where('stage', $num)->values()
    //     ]);

    //     return view('kanban.assets.show', compact('asset', 'stages', 'documentsByStage', 'notesByStage'));
    // }

    public function show(AssetKanban $asset, GoogleDriveService $googleDrive)
    {
        $asset->load([
            'client.parent',
            'documents',
            'notes.user',
            'stageChecks.checker',
        ]);

        try {
            foreach ($asset->documents as $document) {
                $driveFileId = $document->drive_file_id;

                if (!$driveFileId) {
                    $looksLikeDriveId = $document->file_path
                        && !str_contains($document->file_path, '/')
                        && strlen($document->file_path) >= 20;

                    if ($document->storage_disk === 'google_drive' || $looksLikeDriveId) {
                        $driveFileId = $document->file_path;
                    }
                }

                if ($driveFileId && !$googleDrive->fileExists($driveFileId)) {
                    $document->delete();
                }
            }
        } catch (\Throwable $e) {
            Log::warning('Gagal sinkronisasi dokumen Google Drive pada halaman asset.', [
                'asset_id' => $asset->id,
                'error' => $e->getMessage(),
            ]);
        }

        $asset->refresh();

        $asset->load([
            'client.parent',
            'documents',
            'notes.user',
            'stageChecks.checker',
        ]);

        $warningNotes = $asset->notes
            ->whereIn('type', ['rejection', 'blocked'])
            ->sortByDesc('created_at')
            ->values();

        return view('kanban.assets.show', compact('asset', 'warningNotes'));
    }

    /**
     * Show edit form
     */
    public function edit(AssetKanban $asset)
    {
        $clients = ClientKanban::query()
            ->select('id', 'name', 'company_name', 'type', 'parent_id')
            ->orderBy('type')
            ->orderBy('name')
            ->get();

        $clientGroups = $this->getClientGroups($clients);
        $assetTypes = AssetKanban::ASSET_TYPES;

        return view('kanban.assets.edit', compact(
            'asset',
            'clients',
            'clientGroups',
            'assetTypes'
        ));
    }

    /**
     * Update asset with validation
     */
    public function update(Request $request, AssetKanban $asset)
    {
        $validated = $request->validate([
            'client_id' => 'required|exists:clients_kanban,id',
            'name' => 'required|string|max:255|min:3',
            'asset_type' => [
                'required',
                Rule::in(array_keys(AssetKanban::ASSET_TYPES)),
            ],
            'location' => 'nullable|string|max:500',
            'current_stage' => 'nullable|integer|min:1|max:13',
        ], [
            'client_id.required' => 'Klien wajib dipilih.',
            'name.required' => 'Nama asset wajib diisi.',
            'asset_type.required' => 'Tipe asset wajib dipilih.',
        ]);

        $validated['name'] = strip_tags(trim($validated['name']));
        $validated['location'] = !empty($validated['location'])
            ? strip_tags(trim($validated['location']))
            : null;

        $asset->update($validated);

        return redirect()
            ->route('kanban.assets.show', $asset)
            ->with('success', 'Asset berhasil diupdate.');
    }

    /**
     * Soft delete asset
     */
    public function destroy(AssetKanban $asset)
    {
        $assetName = $asset->name;

        $asset->delete();

        return redirect()
            ->route('kanban.assets.index')
            ->with('success', "Asset '{$assetName}' berhasil dihapus.");
    }

    // ==========================================
    // KANBAN OPERATIONS (API)
    // ==========================================

    /**
     * Move asset to specific stage with notification & logging
     */
    public function moveStage(Request $request, AssetKanban $asset)
    {
        $validated = $request->validate([
            'stage' => 'nullable|integer|min:1|max:13',
            'direction' => 'nullable|in:next,prev',
            'note' => 'nullable|string|max:500',
        ]);

        $oldStage = $asset->current_stage;

        // Determine new stage from direction or direct stage
        if (isset($validated['direction'])) {
            $newStage = $validated['direction'] === 'next'
                ? min($oldStage + 1, 13)
                : max($oldStage - 1, 1);
        } elseif (isset($validated['stage'])) {
            $newStage = (int) $validated['stage'];
        } else {
            if ($request->wantsJson()) {
                return response()->json(['success' => false, 'message' => 'Stage atau direction diperlukan'], 400);
            }
            return back()->with('error', 'Stage atau direction diperlukan');
        }

        if ($oldStage === $newStage) {
            if ($request->wantsJson()) {
                return response()->json(['success' => true, 'message' => 'Tidak ada perubahan']);
            }
            return back()->with('info', 'Tidak ada perubahan stage');
        }

        // Use transaction for data integrity
        DB::beginTransaction();
        try {
            $note = $validated['note'] ?? null;
            $success = $asset->moveToStage($newStage, Auth::id(), $note);

            if ($success) {
                // Send notification to admins
                KanbanNotificationService::notifyStageChange(
                    $asset,
                    $oldStage,
                    $newStage,
                    Auth::user(),
                    $note
                );

                DB::commit();

                if ($request->wantsJson()) {
                    return response()->json([
                        'success' => true,
                        'message' => 'Asset dipindahkan ke ' . AssetKanban::STAGES[$newStage],
                        'asset' => $asset->fresh(['client:id,name']),
                        'old_stage' => $oldStage,
                        'new_stage' => $newStage,
                    ]);
                }

                return back()->with('success', 'Asset dipindahkan ke ' . AssetKanban::STAGES[$newStage]);
            }

            DB::rollBack();
            if ($request->wantsJson()) {
                return response()->json(['success' => false, 'message' => 'Gagal memindahkan asset'], 400);
            }
            return back()->with('error', 'Gagal memindahkan asset');
        } catch (\Exception $e) {
            DB::rollBack();
            if ($request->wantsJson()) {
                return response()->json(['success' => false, 'message' => 'Terjadi kesalahan sistem'], 500);
            }
            return back()->with('error', 'Terjadi kesalahan sistem');
        }
    }

    /**
     * Update position within stage (drag & drop)
     */
    public function updatePosition(Request $request, AssetKanban $asset)
    {
        $validated = $request->validate([
            'position' => 'required|integer|min:0|max:9999',
        ]);

        $asset->update(['position' => $validated['position']]);

        return response()->json(['success' => true]);
    }

    public function toggleStageCheck(Request $request, AssetKanban $asset)
    {
        $validated = $request->validate([
            'stage' => 'required|integer|min:1|max:13',
            'is_checked' => 'required|boolean',
            'note' => 'nullable|string|max:500',
        ]);

        $check = AssetStageCheckKanban::updateOrCreate(
            [
                'asset_id' => $asset->id,
                'stage' => $validated['stage'],
            ],
            [
                'is_checked' => $validated['is_checked'],
                'checked_by' => $validated['is_checked'] ? Auth::id() : null,
                'checked_at' => $validated['is_checked'] ? now() : null,
                'note' => !empty($validated['note'])
                    ? strip_tags(trim($validated['note']))
                    : null,
            ]
        );

        $message = $validated['is_checked']
            ? 'Stage berhasil ditandai sudah dicek.'
            : 'Checklist stage berhasil dibatalkan.';

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => $message,
                'check' => $check,
            ]);
        }

        return back()->with('success', $message);
    }
}

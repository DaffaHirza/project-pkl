<?php

namespace App\Http\Controllers\Kanban;

use App\Http\Controllers\Controller;
use App\Models\ClientKanban;
use App\Models\User;
use App\Models\Notification;
use App\Models\AssetKanban;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class ClientController extends Controller
{
    /**
     * Index - Show type selector with stats
     */
    public function index()
    {
        $stats = [
            'bank' => ClientKanban::where('type', 'bank')->count(),
            'pt_cv_induk' => ClientKanban::where('type', 'pt_cv')->whereNull('parent_id')->count(),
            'debitur' => ClientKanban::where('type', 'debitur')->count(),
            'pt_cv_anak' => ClientKanban::where('type', 'pt_cv')->whereNotNull('parent_id')->count(),
        ];

        return view('kanban.clients.index', compact('stats'));
    }

    /**
     * List Bank & PT/CV Induk (Perusahaan)
     */
    public function indexPerusahaan(Request $request)
    {
        $query = ClientKanban::query()
            ->select('id', 'name', 'company_name', 'spk_number', 'type', 'created_at')
            ->where(function ($q) {
                $q->where('type', 'bank')
                    ->orWhere(function ($q2) {
                        $q2->where('type', 'pt_cv')->whereNull('parent_id');
                    });
            })
            ->withCount(['children', 'assets']);

        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('company_name', 'like', "%{$search}%")
                    ->orWhere('spk_number', 'like', "%{$search}%");
            });
        }

        if ($request->filled('type') && in_array($request->type, ['bank', 'pt_cv'])) {
            $query->where('type', $request->type);
        }

        $clients = $query->latest()->paginate(15)->withQueryString();

        return view('kanban.clients.perusahaan', compact('clients'));
    }

    /**
     * List Debitur & PT/CV Anak
     */
    public function indexDebitur(Request $request)
    {
        $query = ClientKanban::query()
            ->select('id', 'name', 'company_name', 'type', 'parent_id', 'created_at')
            ->where(function ($q) {
                $q->where('type', 'debitur')
                    ->orWhere(function ($q2) {
                        $q2->where('type', 'pt_cv')->whereNotNull('parent_id');
                    });
            })
            ->with('parent:id,name,type,company_name')
            ->withCount('assets');

        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('company_name', 'like', "%{$search}%");
            });
        }

        if ($request->filled('parent_id')) {
            $query->where('parent_id', (int) $request->parent_id);
        }

        $clients = $query->latest()->paginate(15)->withQueryString();

        // Get parent companies for filter dropdown
        $parentCompanies = ClientKanban::where(function ($q) {
            $q->where('type', 'bank')
                ->orWhere(function ($q2) {
                    $q2->where('type', 'pt_cv')->whereNull('parent_id');
                });
        })->select('id', 'name', 'type')->orderBy('name')->get();

        return view('kanban.clients.debitur', compact('clients', 'parentCompanies'));
    }

    /**
     * Show create type selector
     */
    public function create()
    {
        return view('kanban.clients.create');
    }

    /**
     * Bank creation form - with multiple debiturs
     */
    public function createBank()
    {
        return view('kanban.clients.bank.create-bank');
    }

    /**
     * PT/CV Induk creation form - with multiple children
     */
    public function createPerusahaanInduk()
    {
        return view('kanban.clients.perusahaan-induk.create-perusahaan-induk');
    }

    /**
     * Simple form for Debitur or PT/CV Anak
     */
    public function createKlien()
    {
        // Get parent companies for dropdown
        $parentCompanies = ClientKanban::where(function ($q) {
            $q->where('type', 'bank')
                ->orWhere(function ($q2) {
                    $q2->where('type', 'pt_cv')->whereNull('parent_id');
                });
        })->select('id', 'name', 'type', 'company_name')->orderBy('type')->orderBy('name')->get();

        return view('kanban.clients.create-klien', compact('parentCompanies'));
    }

    /**
     * Store Bank with multiple debiturs
     */
    public function storeBank(Request $request)
    {
        $validated = $request->validate([
            'company_name' => [
                'required',
                'string',
                'max:255',
                'min:2',
                Rule::unique('clients_kanban', 'name')
                    ->where(fn($query) => $query->where('type', 'bank')),
            ],
            'spk_number' => 'nullable|string|max:100',
            'debiturs' => 'required|array|min:1',
            'debiturs.*.name' => [
                'required',
                'string',
                'max:255',
                'min:2',
                'distinct:ignore_case',
                Rule::unique('clients_kanban', 'name')
                    ->where(fn($query) => $query->where('type', 'debitur')),
            ],
            'debiturs.*.company_name' => 'nullable|string|max:255',
        ], [
            'company_name.required' => 'Nama bank wajib diisi.',
            'company_name.unique' => 'Nama bank sudah terdaftar.',
            'debiturs.required' => 'Minimal 1 debitur harus ditambahkan.',
            'debiturs.*.name.required' => 'Nama debitur wajib diisi.',
            'debiturs.*.name.unique' => 'Nama debitur sudah terdaftar.',
            'debiturs.*.name.distinct' => 'Nama debitur tidak boleh sama dalam satu form.',
        ]);

        DB::beginTransaction();
        try {
            // Create bank
            $bank = ClientKanban::create([
                'name' => strip_tags(trim($validated['company_name'])),
                'company_name' => strip_tags(trim($validated['company_name'])),
                'spk_number' => $validated['spk_number'] ? strip_tags(trim($validated['spk_number'])) : null,
                'type' => 'bank',
            ]);

            // Create debiturs
            foreach ($validated['debiturs'] as $debiturData) {
                ClientKanban::create([
                    'name' => strip_tags(trim($debiturData['name'])),
                    'company_name' => !empty($debiturData['company_name']) ? strip_tags(trim($debiturData['company_name'])) : null,
                    'type' => 'debitur',
                    'parent_id' => $bank->id,
                ]);
            }

            DB::commit();

            $this->notifyAdmins('client_created', [
                'title' => 'Bank Baru',
                'message' => Auth::user()->name . " menambahkan bank: {$bank->company_name} dengan " . count($validated['debiturs']) . " debitur",
                'action_url' => route('kanban.clients.show', $bank->id),
            ]);

            return redirect()
                ->route('kanban.clients.show', $bank)
                ->with('success', 'Bank dan debitur berhasil ditambahkan.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Store PT/CV Induk with multiple children
     */
    public function storePerusahaanInduk(Request $request)
    {
        $validated = $request->validate([
            'company_name' => [
                'required',
                'string',
                'max:255',
                'min:2',
                Rule::unique('clients_kanban', 'name')
                    ->where(
                        fn($query) => $query
                            ->where('type', 'pt_cv')
                            ->whereNull('parent_id')
                    ),
            ],
            'spk_number' => 'nullable|string|max:100',
            'children' => 'nullable|array',
            'children.*.company_name' => [
                'required_with:children',
                'string',
                'max:255',
                'min:2',
                'distinct:ignore_case',
                Rule::unique('clients_kanban', 'name')
                    ->where(
                        fn($query) => $query
                            ->where('type', 'pt_cv')
                            ->whereNotNull('parent_id')
                    ),
            ],
        ], [
            'company_name.required' => 'Nama perusahaan wajib diisi.',
            'company_name.unique' => 'Nama PT/CV induk sudah terdaftar.',
            'children.*.company_name.required_with' => 'Nama PT/CV anak wajib diisi.',
            'children.*.company_name.unique' => 'Nama PT/CV anak sudah terdaftar.',
            'children.*.company_name.distinct' => 'Nama PT/CV anak tidak boleh sama dalam satu form.',
        ]);

        DB::beginTransaction();
        try {
            // Create parent PT/CV
            $parent = ClientKanban::create([
                'name' => strip_tags(trim($validated['company_name'])),
                'company_name' => strip_tags(trim($validated['company_name'])),
                'spk_number' => !empty($validated['spk_number']) ? strip_tags(trim($validated['spk_number'])) : null,
                'type' => 'pt_cv',
            ]);

            // Create child PT/CVs if any
            if (!empty($validated['children'])) {
                foreach ($validated['children'] as $childData) {
                    if (!empty($childData['company_name'])) {
                        ClientKanban::create([
                            'name' => strip_tags(trim($childData['company_name'])),
                            'company_name' => strip_tags(trim($childData['company_name'])),
                            'type' => 'pt_cv',
                            'parent_id' => $parent->id,
                        ]);
                    }
                }
            }

            DB::commit();

            $childCount = !empty($validated['children']) ? count($validated['children']) : 0;
            $this->notifyAdmins('client_created', [
                'title' => 'Perusahaan Baru',
                'message' => Auth::user()->name . " menambahkan perusahaan: {$parent->company_name}" . ($childCount > 0 ? " dengan {$childCount} PT/CV anak" : ""),
                'action_url' => route('kanban.clients.show', $parent->id),
            ]);

            return redirect()
                ->route('kanban.clients.show', $parent)
                ->with('success', 'Perusahaan berhasil ditambahkan.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Store single Klien (Debitur or PT/CV Anak)
     */
    public function storeKlien(Request $request)
    {
        $clientType = $request->input('client_type');

        $nameUniqueRule = Rule::unique('clients_kanban', 'name');

        if ($clientType === 'debitur') {
            $nameUniqueRule = $nameUniqueRule
                ->where(fn($query) => $query->where('type', 'debitur'));
        }

        if ($clientType === 'pt_cv_anak') {
            $nameUniqueRule = $nameUniqueRule
                ->where(
                    fn($query) => $query
                        ->where('type', 'pt_cv')
                        ->whereNotNull('parent_id')
                );
        }

        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                'min:2',
                $nameUniqueRule,
            ],
            'company_name' => 'nullable|string|max:255',
            'client_type' => 'required|in:debitur,pt_cv_anak',
            'parent_id' => 'required|exists:clients_kanban,id',
        ], [
            'name.required' => 'Nama klien wajib diisi.',
            'name.unique' => $clientType === 'pt_cv_anak'
                ? 'Nama PT/CV anak sudah terdaftar.'
                : 'Nama debitur sudah terdaftar.',
            'client_type.required' => 'Tipe klien wajib dipilih.',
            'parent_id.required' => 'Induk (Bank/PT) wajib dipilih.',
        ]);

        $client = ClientKanban::create([
            'name' => strip_tags(trim($validated['name'])),
            'company_name' => $validated['company_name'] ? strip_tags(trim($validated['company_name'])) : null,
            'type' => $validated['client_type'] === 'pt_cv_anak' ? 'pt_cv' : 'debitur',
            'parent_id' => $validated['parent_id'],
        ]);

        $this->notifyAdmins('client_created', [
            'title' => 'Klien Baru',
            'message' => Auth::user()->name . " menambahkan klien: {$client->name}",
            'action_url' => route('kanban.clients.show', $client->id),
        ]);

        return redirect()
            ->route('kanban.clients.show', $client)
            ->with('success', 'Klien berhasil ditambahkan.');
    }

    public function show(ClientKanban $client)
    {
        $client->load([
            'assets' => fn($q) => $q
                ->select('id', 'client_id', 'name', 'asset_type', 'current_stage', 'created_at')
                ->latest()
                ->limit(20),
            'children' => fn($q) => $q->select('id', 'parent_id', 'name', 'type', 'company_name')->withCount('assets'),
            'parent:id,name,type,company_name,spk_number'
        ]);

        return view('kanban.clients.show', compact('client'));
    }

    public function edit(ClientKanban $client)
    {
        $parentClients = ClientKanban::where(function ($q) {
            $q->where('type', 'bank')
                ->orWhere(function ($q2) {
                    $q2->where('type', 'pt_cv')->whereNull('parent_id');
                });
        })
            ->where('id', '!=', $client->id)
            ->select('id', 'name', 'type', 'company_name')
            ->orderBy('type')
            ->orderBy('name')
            ->get();

        return view('kanban.clients.edit', compact('client', 'parentClients'));
    }

    public function update(Request $request, ClientKanban $client)
    {
        $type = $request->input('type');
        $parentId = $request->input('parent_id');

        $nameUniqueRule = Rule::unique('clients_kanban', 'name')->ignore($client->id);

        if ($type === 'bank') {
            $nameUniqueRule = $nameUniqueRule
                ->where(fn($query) => $query->where('type', 'bank'));
        }

        if ($type === 'debitur') {
            $nameUniqueRule = $nameUniqueRule
                ->where(fn($query) => $query->where('type', 'debitur'));
        }

        if ($type === 'pt_cv' && empty($parentId)) {
            $nameUniqueRule = $nameUniqueRule
                ->where(
                    fn($query) => $query
                        ->where('type', 'pt_cv')
                        ->whereNull('parent_id')
                );
        }

        if ($type === 'pt_cv' && !empty($parentId)) {
            $nameUniqueRule = $nameUniqueRule
                ->where(
                    fn($query) => $query
                        ->where('type', 'pt_cv')
                        ->whereNotNull('parent_id')
                );
        }

        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                'min:2',
                $nameUniqueRule,
            ],
            'company_name' => 'nullable|string|max:255',
            'spk_number' => 'nullable|string|max:100',
            'type' => 'required|in:bank,pt_cv,debitur',
            'parent_id' => 'nullable|exists:clients_kanban,id',
        ], [
            'name.required' => 'Nama client wajib diisi.',
            'name.unique' => 'Nama client sudah terdaftar pada kategori yang sama.',
            'type.required' => 'Tipe client wajib dipilih.',
        ]);

        $validated['name'] = strip_tags(trim($validated['name']));
        $validated['company_name'] = $validated['company_name'] ? strip_tags(trim($validated['company_name'])) : null;
        $validated['spk_number'] = $validated['spk_number'] ? strip_tags(trim($validated['spk_number'])) : null;
        $validated['parent_id'] = $validated['parent_id'] ?: null;

        $client->update($validated);

        return redirect()
            ->route('kanban.clients.show', $client)
            ->with('success', 'Client berhasil diupdate.');
    }

    public function destroy(ClientKanban $client)
    {
        $isParentClient = $client->parent_id === null && in_array($client->type, ['bank', 'pt_cv'], true);

        $redirectRoute = $isParentClient
            ? 'kanban.clients.perusahaan'
            : 'kanban.clients.debitur';

        $clientLabel = $isParentClient
            ? ($client->type === 'bank' ? 'Bank' : 'PT/CV Induk')
            : ($client->type === 'debitur' ? 'Debitur' : 'PT/CV Anak');

        $clientName = $client->company_name ?? $client->name;

        DB::beginTransaction();

        try {
            // Ambil ID client utama + semua child-nya
            // Contoh: Bank -> Debitur
            // Contoh: PT Induk -> PT/CV Anak
            $clientIds = $this->collectClientTreeIds($client);

            // Hitung jumlah data turunan
            $childCount = count($clientIds) - 1;

            // Hapus semua asset yang terhubung ke client utama maupun child-nya
            // Pakai each->delete() agar event model tetap jalan kalau ada
            $assets = AssetKanban::whereIn('client_id', $clientIds)->get();
            $assetCount = $assets->count();

            $assets->each->delete();

            // Hapus child terlebih dahulu, lalu hapus parent
            $descendantIds = array_values(array_diff($clientIds, [$client->id]));

            foreach (array_reverse($descendantIds) as $descendantId) {
                ClientKanban::whereKey($descendantId)->delete();
            }

            $client->delete();

            DB::commit();

            return redirect()
                ->route($redirectRoute)
                ->with(
                    'success',
                    "{$clientLabel} '{$clientName}' berhasil dihapus beserta {$childCount} data turunan dan {$assetCount} asset terkait."
                );
        } catch (\Throwable $e) {
            DB::rollBack();

            return redirect()
                ->route($redirectRoute)
                ->with('error', 'Gagal menghapus data: ' . $e->getMessage());
        }
    }

    private function collectClientTreeIds(ClientKanban $client): array
    {
        $ids = [$client->id];

        $children = ClientKanban::where('parent_id', $client->id)->get();

        foreach ($children as $child) {
            $ids = array_merge($ids, $this->collectClientTreeIds($child));
        }

        return $ids;
    }

    /**
     * API: Search clients for autocomplete (min 2 chars)
     */
    public function search(Request $request)
    {
        $search = trim($request->get('q', ''));

        if (strlen($search) < 2) {
            return response()->json([]);
        }

        $clients = ClientKanban::query()
            ->select('id', 'name', 'company_name', 'type')
            ->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('company_name', 'like', "%{$search}%");
            })
            ->limit(10)
            ->get();

        return response()->json($clients);
    }

    /**
     * Helper: Notify all other users (admins)
     */
    private function notifyAdmins(string $type, array $data): void
    {
        User::where('id', '!=', Auth::id())->chunk(100, function ($admins) use ($type, $data) {
            foreach ($admins as $admin) {
                Notification::notify($admin, $type, $data);
            }
        });
    }
}

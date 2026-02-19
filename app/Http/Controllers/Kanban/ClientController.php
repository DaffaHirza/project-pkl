<?php

namespace App\Http\Controllers\Kanban;

use App\Http\Controllers\Controller;
use App\Models\ClientKanban;
use App\Models\User;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ClientController extends Controller
{
    /**
     * Display paginated list of clients with search
     */
    public function index(Request $request)
    {
        $query = ClientKanban::query()
            ->select('id', 'name', 'company_name', 'email', 'phone', 'created_at')
            ->withCount('projects');

        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('company_name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $clients = $query->latest()->paginate(15)->withQueryString();

        return view('kanban.clients.index', compact('clients'));
    }

    public function create()
    {
        return view('kanban.clients.create');
    }

    /**
     * Store with validation, sanitization & notification
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|min:2',
            'company_name' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255|unique:clients_kanban,email',
            'phone' => 'nullable|string|max:50',
            'address' => 'nullable|string|max:1000',
        ], [
            'name.required' => 'Nama client wajib diisi.',
            'email.unique' => 'Email sudah terdaftar.',
        ]);

        // Sanitize input
        $validated = array_map(fn($v) => is_string($v) ? strip_tags(trim($v)) : $v, $validated);

        $client = ClientKanban::create($validated);

        // Notify admins
        $this->notifyAdmins('client_created', [
            'title' => 'Client Baru',
            'message' => Auth::user()->name . " menambahkan client: {$client->name}",
            'action_url' => route('kanban.clients.show', $client->id),
        ]);

        return redirect()
            ->route('kanban.clients.show', $client)
            ->with('success', 'Client berhasil ditambahkan.');
    }

    public function show(ClientKanban $client)
    {
        $client->load([
            'projects' => fn($q) => $q
                ->select('id', 'client_id', 'name', 'project_code', 'status', 'due_date', 'created_at')
                ->withCount('assets')
                ->latest()
                ->limit(10)
        ]);

        return view('kanban.clients.show', compact('client'));
    }

    public function edit(ClientKanban $client)
    {
        return view('kanban.clients.edit', compact('client'));
    }

    public function update(Request $request, ClientKanban $client)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|min:2',
            'company_name' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255|unique:clients_kanban,email,' . $client->id,
            'phone' => 'nullable|string|max:50',
            'address' => 'nullable|string|max:1000',
        ]);

        $validated = array_map(fn($v) => is_string($v) ? strip_tags(trim($v)) : $v, $validated);

        $client->update($validated);

        return redirect()
            ->route('kanban.clients.show', $client)
            ->with('success', 'Client berhasil diupdate.');
    }

    public function destroy(ClientKanban $client)
    {
        if ($client->projects()->exists()) {
            return back()->with('error', 'Tidak bisa menghapus client yang masih memiliki project.');
        }

        $clientName = $client->name;
        $client->delete();

        return redirect()
            ->route('kanban.clients.index')
            ->with('success', "Client '{$clientName}' berhasil dihapus.");
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
            ->select('id', 'name', 'company_name')
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

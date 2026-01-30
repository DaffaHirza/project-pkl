<?php

namespace App\Http\Controllers\Appraisal;

use App\Http\Controllers\Controller;
use App\Models\KanbanClient;
use Illuminate\Http\Request;

class KanbanClientController extends Controller
{
    /**
     * Display a listing of clients
     */
    public function index(Request $request)
    {
        $query = KanbanClient::query();

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('company_name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $clients = $query->withCount('projects')
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('appraisal.clients.index', compact('clients'));
    }

    /**
     * Show the form for creating a new client
     */
    public function create()
    {
        return view('appraisal.clients.create');
    }

    /**
     * Store a newly created client
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'company_name' => 'nullable|string|max:255',
            'email' => 'required|email|unique:kanban_clients,email',
            'phone' => 'required|string|max:20',
            'address' => 'required|string',
        ]);

        $client = KanbanClient::create($validated);

        return redirect()
            ->route('appraisal.clients.show', $client)
            ->with('success', 'Klien berhasil ditambahkan.');
    }

    /**
     * Display the specified client
     */
    public function show(KanbanClient $client)
    {
        $client->load(['projects' => function ($query) {
            $query->orderBy('created_at', 'desc')->limit(10);
        }]);

        return view('appraisal.clients.show', compact('client'));
    }

    /**
     * Show the form for editing the specified client
     */
    public function edit(KanbanClient $client)
    {
        return view('appraisal.clients.edit', compact('client'));
    }

    /**
     * Update the specified client
     */
    public function update(Request $request, KanbanClient $client)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'company_name' => 'nullable|string|max:255',
            'email' => 'required|email|unique:kanban_clients,email,' . $client->id,
            'phone' => 'required|string|max:20',
            'address' => 'required|string',
        ]);

        $client->update($validated);

        return redirect()
            ->route('appraisal.clients.show', $client)
            ->with('success', 'Data klien berhasil diperbarui.');
    }

    /**
     * Remove the specified client
     */
    public function destroy(KanbanClient $client)
    {
        // Check if client has projects
        if ($client->projects()->exists()) {
            return back()->with('error', 'Tidak dapat menghapus klien yang memiliki project.');
        }

        $client->delete();

        return redirect()
            ->route('appraisal.clients.index')
            ->with('success', 'Klien berhasil dihapus.');
    }

    /**
     * API: Search clients for autocomplete
     */
    public function search(Request $request)
    {
        $search = $request->get('q', '');
        
        $clients = KanbanClient::where('name', 'like', "%{$search}%")
            ->orWhere('company_name', 'like', "%{$search}%")
            ->limit(10)
            ->get(['id', 'name', 'company_name', 'email']);

        return response()->json($clients);
    }
}

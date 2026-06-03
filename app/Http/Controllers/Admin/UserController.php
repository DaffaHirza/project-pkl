<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $search = trim((string) $request->string('search'));
        $role = $request->string('role')->toString();

        $allowedRoles = [
            User::ROLE_USER,
            User::ROLE_ADMIN,
            User::ROLE_SUPERUSER,
        ];

        $users = User::query()
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($innerQuery) use ($search) {
                    $innerQuery
                        ->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                });
            })
            ->when(in_array($role, $allowedRoles, true), function ($query) use ($role) {
                $query->where('role', $role);
            })
            ->orderByDesc('id')
            ->paginate(10)
            ->withQueryString();

        // Get statistics
        $totalUsers = User::count();
        $totalTokens = User::sum('jumlah_token') ?? 0;

        return view('admin.users.index', [
            'users' => $users,
            'search' => $search,
            'role' => $role,
            'totalUsers' => $totalUsers,
            'totalTokens' => $totalTokens,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.users.create', [
            'roles' => [
                User::ROLE_USER,
                User::ROLE_ADMIN,
                User::ROLE_SUPERUSER,
            ],
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'role' => ['required', Rule::in([User::ROLE_USER, User::ROLE_ADMIN, User::ROLE_SUPERUSER])],
            'is_active' => ['nullable', 'boolean'],
            'telegram_chat_id' => ['nullable', 'string', 'max:255'],
            'jumlah_token' => ['nullable', 'integer', 'min:0'],
        ]);

        User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => $validated['role'],
            'is_active' => (bool) ($validated['is_active'] ?? true),
            'telegram_chat_id' => $validated['telegram_chat_id'] ?? null,
            'jumlah_token' => (int) ($validated['jumlah_token'] ?? 0),
        ]);

        return redirect()
            ->route('admin.users.index')
            ->with('success', 'User baru berhasil dibuat.');
    }

    /**
     * Display the specified resource.
     */
    public function show(User $user)
    {
        return view('admin.users.show', [
            'user' => $user,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $user)
    {
        return view('admin.users.edit', [
            'user' => $user,
            'roles' => [
                User::ROLE_USER,
                User::ROLE_ADMIN,
                User::ROLE_SUPERUSER,
            ],
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $user->id],
            'role' => ['required', Rule::in([User::ROLE_USER, User::ROLE_ADMIN, User::ROLE_SUPERUSER])],
            'is_active' => ['nullable', 'boolean'],
            'telegram_chat_id' => ['nullable', 'string', 'max:255'],
            'jumlah_token' => ['nullable', 'integer', 'min:0'],
        ]);

        $user->update([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'role' => $validated['role'],
            'is_active' => (bool) $validated['is_active'],
            'telegram_chat_id' => $validated['telegram_chat_id'] ?? null,
            'jumlah_token' => (int) ($validated['jumlah_token'] ?? $user->jumlah_token),
        ]);

        return redirect()
            ->route('admin.users.show', $user)
            ->with('success', 'User berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        // Prevent deleting the current user
        if ($user->id === Auth::id()) {
            return redirect()
                ->route('admin.users.index')
                ->with('error', 'Tidak dapat menghapus akun Anda sendiri.');
        }

        $user->delete();

        return redirect()
            ->route('admin.users.index')
            ->with('success', 'User berhasil dihapus.');
    }
}

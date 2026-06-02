@extends('layouts.app')

@section('title', 'Manajemen Akun')

@section('content')
    <div class="space-y-6">
        <div class="rounded-2xl bg-gradient-to-r from-brand-500 to-brand-600 p-6 text-white">
            <div class="flex flex-col gap-4 md:flex-row md:items-end md:justify-between">
                <div>
                    <h1 class="text-2xl font-bold">Manajemen Akun</h1>
                    <p class="mt-1 text-sm text-brand-100">Kelola data akun user, admin, dan superuser dari satu halaman.</p>
                </div>
                <a href="{{ route('admin.users.create') }}"
                    class="inline-flex items-center justify-center gap-2 rounded-xl bg-white px-4 py-2.5 text-sm font-semibold text-brand-600 transition hover:bg-brand-50">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v14m-7-7h14" />
                    </svg>
                    Create User
                </a>
            </div>
        </div>

        @if (session('success'))
            <div
                class="rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700 dark:border-emerald-900/60 dark:bg-emerald-950/40 dark:text-emerald-300">
                {{ session('success') }}
            </div>
        @endif

        <!-- Statistics Cards -->
        <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
            <!-- Total Users -->
            <div class="rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-gray-900">
                <div class="flex items-start justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Total Akun</p>
                        <p class="mt-2 text-3xl font-bold text-gray-900 dark:text-white">{{ $totalUsers }}</p>
                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">User, admin, dan superuser</p>
                    </div>
                    <div class="rounded-lg bg-blue-100 p-3 dark:bg-blue-900/30">
                        <svg class="h-6 w-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 4.354a4 4 0 110 5.292M15 21H3v-2a6 6 0 0112 0v2zm0 0h6v-2a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Total Tokens -->
            <div class="rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-gray-900">
                <div class="flex items-start justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Total Token</p>
                        <p class="mt-2 text-3xl font-bold text-gray-900 dark:text-white">{{ number_format($totalTokens) }}
                        </p>
                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Dari semua akun</p>
                    </div>
                    <div class="rounded-lg bg-brand-100 p-3 dark:bg-brand-900/30">
                        <svg class="h-6 w-6 text-brand-600 dark:text-brand-400" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <div class="rounded-2xl border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-gray-900">
            <form method="GET" action="{{ route('admin.users.index') }}" class="grid grid-cols-1 gap-3 md:grid-cols-3">
                <div class="md:col-span-2">
                    <label for="search"
                        class="mb-1 block text-xs font-semibold uppercase tracking-wide text-gray-500">Cari
                        akun</label>
                    <input id="search" name="search" type="text" value="{{ $search }}"
                        placeholder="Nama atau email"
                        class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm text-gray-700 focus:border-brand-400 focus:outline-none focus:ring-2 focus:ring-brand-200 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-200" />
                </div>
                <div>
                    <label for="role"
                        class="mb-1 block text-xs font-semibold uppercase tracking-wide text-gray-500">Role</label>
                    <select id="role" name="role"
                        class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm text-gray-700 focus:border-brand-400 focus:outline-none focus:ring-2 focus:ring-brand-200 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-200">
                        <option value="">Semua Role</option>
                        <option value="{{ \App\Models\User::ROLE_USER }}" @selected($role === \App\Models\User::ROLE_USER)>User</option>
                        <option value="{{ \App\Models\User::ROLE_ADMIN }}" @selected($role === \App\Models\User::ROLE_ADMIN)>Admin</option>
                        <option value="{{ \App\Models\User::ROLE_SUPERUSER }}" @selected($role === \App\Models\User::ROLE_SUPERUSER)>Superuser
                        </option>
                    </select>
                </div>
                <div class="md:col-span-3 flex gap-2">
                    <button type="submit"
                        class="rounded-lg bg-brand-500 px-4 py-2 text-sm font-semibold text-white transition hover:bg-brand-600">
                        Terapkan Filter
                    </button>
                    <a href="{{ route('admin.users.index') }}"
                        class="rounded-lg border border-gray-300 px-4 py-2 text-sm font-semibold text-gray-700 transition hover:bg-gray-50 dark:border-gray-700 dark:text-gray-200 dark:hover:bg-gray-800">
                        Reset
                    </a>
                </div>
            </form>
        </div>

        <div class="overflow-hidden rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-800">
                    <thead class="bg-gray-50 dark:bg-gray-800/60">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">Nama
                            </th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">
                                Email</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">Role
                            </th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">
                                Status</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">Last
                                Login</th>

                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">
                                Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                        @forelse ($users as $user)
                            @php
                                $initial = mb_strtoupper(mb_substr(trim($user->name), 0, 1));
                                $hue = abs(crc32((string) $user->name)) % 360;
                                $avatarBg = "hsl({$hue} 85% 88%)";
                                $avatarText = "hsl({$hue} 65% 30%)";
                            @endphp
                            <tr class="hover:bg-gray-50/70 dark:hover:bg-gray-800/40">
                                <td class="px-4 py-3 text-sm font-medium text-gray-800 dark:text-gray-100">
                                    <div class="flex items-center gap-3">
                                        <div class="flex h-9 w-9 items-center justify-center rounded-full text-sm font-bold"
                                            style="background-color: {{ $avatarBg }}; color: {{ $avatarText }};">
                                            {{ $initial !== '' ? $initial : '?' }}
                                        </div>
                                        <span>{{ $user->name }}</span>
                                    </div>
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-300">{{ $user->email }}</td>
                                <td class="px-4 py-3 text-sm">
                                    @php
                                        $roleBadgeClasses = match ($user->role) {
                                            \App\Models\User::ROLE_SUPERUSER
                                                => 'bg-purple-100 text-purple-700 dark:bg-purple-900/40 dark:text-purple-300',
                                            \App\Models\User::ROLE_ADMIN
                                                => 'bg-amber-100 text-amber-700 dark:bg-amber-900/40 dark:text-amber-300',
                                            default
                                                => 'bg-slate-100 text-slate-700 dark:bg-slate-700/60 dark:text-slate-200',
                                        };
                                    @endphp
                                    <span
                                        class="inline-flex rounded-full px-2.5 py-1 text-xs font-semibold {{ $roleBadgeClasses }}">
                                        {{ ucfirst($user->role) }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-sm">
                                    @if ($user->is_active)
                                        <span class="inline-flex items-center gap-1 text-emerald-600 dark:text-emerald-400">
                                            <span class="h-1.5 w-1.5 rounded-full bg-emerald-500"></span>
                                            Aktif
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 text-red-600 dark:text-red-400">
                                            <span class="h-1.5 w-1.5 rounded-full bg-red-500"></span>
                                            Nonaktif
                                        </span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-300">
                                    {{ $user->last_login_at?->diffForHumans() ?? 'Belum pernah login' }}
                                </td>

                                <td class="px-6 py-4 align-top">
                                    <div class="flex gap-2">
                                        <a href="{{ route('admin.users.show', $user) }}"
                                            class="py-2 px-3 text-sm font-medium text-white bg-[#4F46E5] rounded-lg hover:bg-[#4F46E5]-700"
                                            title="View Detail">
                                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none"
                                                xmlns="http://www.w3.org/2000/svg">
                                                <path
                                                    d="M12 15C13.6568 15 15 13.6568 15 12C15 10.3432 13.6568 9 12 9C10.3432 9 9 10.3432 9 12C9 13.6568 10.3432 15 12 15Z"
                                                    fill="white" />
                                                <path fill-rule="evenodd" clip-rule="evenodd"
                                                    d="M12 19C17.5228 19 22 14.1538 22 12C22 9.84615 17.5228 5 12 5C6.47717 5 2 9.84615 2 12C2 14.1538 6.47717 19 12 19Z"
                                                    stroke="white" stroke-width="1.5" stroke-linecap="round"
                                                    stroke-linejoin="round" />
                                                <path
                                                    d="M12 15C13.6568 15 15 13.6568 15 12C15 10.3432 13.6568 9 12 9C10.3432 9 9 10.3432 9 12C9 13.6568 10.3432 15 12 15Z"
                                                    stroke="white" stroke-width="1.5" stroke-linecap="round"
                                                    stroke-linejoin="round" />
                                            </svg>
                                        </a>
                                        <a href="{{ route('admin.users.edit', $user) }}"
                                            class="py-2 px-3 text-sm font-medium text-white bg-[#0EA5E9] rounded-lg hover:bg-[#0EA5E9]-700"
                                            title="Edit Profile">
                                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none"
                                                xmlns="http://www.w3.org/2000/svg">
                                                <path
                                                    d="M15.1875 5.42383C15.6118 5.46926 15.9499 5.66401 16.2188 5.86914C16.503 6.08603 16.8078 6.39374 17.1211 6.70703L17.293 6.87891C17.6063 7.1922 17.914 7.49698 18.1309 7.78125C18.3653 8.08862 18.5859 8.48644 18.5859 9C18.5859 9.51356 18.3653 9.91138 18.1309 10.2188C17.914 10.503 17.6063 10.8078 17.293 11.1211L10.0986 18.3154C9.94157 18.4725 9.73819 18.6886 9.47461 18.8379C9.21089 18.9872 8.92063 19.0506 8.70508 19.1045L6.09668 19.7559L6.09473 19.7568L6.05078 19.7676C5.90293 19.8045 5.68156 19.8628 5.4873 19.8818C5.28061 19.9021 4.82874 19.9088 4.45996 19.54C4.09118 19.1713 4.09794 18.7194 4.11816 18.5127C4.13719 18.3184 4.19546 18.0971 4.23242 17.9492L4.89551 15.2949C4.9494 15.0794 5.0128 14.7891 5.16211 14.5254C5.31138 14.2618 5.5275 14.0584 5.68457 13.9014L12.8789 6.70703C13.1922 6.39374 13.497 6.08603 13.7812 5.86914C14.0886 5.63466 14.4864 5.41406 15 5.41406L15.1875 5.42383Z"
                                                    stroke="#FFFFFF" stroke-width="2" />
                                                <path d="M12.5 7.5L15.5 5.5L18.5 8.5L16.5 11.5L12.5 7.5Z" fill="#FFFFFF" />
                                            </svg>
                                        </a>
                                        <form action="{{ route('admin.users.destroy', $user) }}" method="POST"
                                            onsubmit="return confirm('Yakin mau hapus user ini?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                class="py-2 px-3 text-sm font-medium text-white bg-red-500 rounded-lg hover:bg-red-700">
                                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none"
                                                    xmlns="http://www.w3.org/2000/svg">
                                                    <path d="M10 15L10 12" stroke="#FFFFFF" stroke-width="2"
                                                        stroke-linecap="round" />
                                                    <path d="M14 15L14 12" stroke="#FFFFFF" stroke-width="2"
                                                        stroke-linecap="round" />
                                                    <path
                                                        d="M3 7H21C20.0681 7 19.6022 7 19.2346 7.15224C18.7446 7.35523 18.3552 7.74458 18.1522 8.23463C18 8.60218 18 9.06812 18 10V16C18 17.8856 18 18.8284 17.4142 19.4142C16.8284 20 15.8856 20 14 20H10C8.11438 20 7.17157 20 6.58579 19.4142C6 18.8284 6 17.8856 6 16V10C6 9.06812 6 8.60218 5.84776 8.23463C5.64477 7.74458 5.25542 7.35523 4.76537 7.15224C4.39782 7 3.93188 7 3 7Z"
                                                        stroke="#FFFFFF" stroke-width="2" stroke-linecap="round" />
                                                    <path
                                                        d="M10.0681 3.37059C10.1821 3.26427 10.4332 3.17033 10.7825 3.10332C11.1318 3.03632 11.5597 3 12 3C12.4403 3 12.8682 3.03632 13.2175 3.10332C13.5668 3.17033 13.8179 3.26427 13.9319 3.37059"
                                                        stroke="#FFFFFF" stroke-width="2" stroke-linecap="round" />
                                                </svg>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5"
                                    class="px-4 py-10 text-center text-sm text-gray-500 dark:text-gray-400">
                                    Data akun tidak ditemukan.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($users->hasPages())
                <div class="border-t border-gray-200 px-4 py-3 dark:border-gray-800">
                    {{ $users->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection

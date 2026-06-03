@extends('admin.layouts.app')

@section('title', 'Detail User - ' . $user->name)

@section('content')
    <div class="space-y-6">
        <!-- Header -->
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">{{ $user->name }}</h1>
                <p class="mt-1 text-sm text-gray-500">Detail akun pengguna</p>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('admin.users.index') }}"
                    class="inline-flex items-center gap-2 rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 transition hover:bg-gray-50">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                    Kembali
                </a>
            </div>
        </div>

        <div class="grid gap-6 lg:grid-cols-3">
            <!-- Profile Card -->
            <div class="lg:col-span-1">
                <div
                    class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-800 dark:bg-gray-900">
                    <!-- Avatar -->
                    <div class="mb-6 flex justify-center">
                        <div class="relative">
                            <img src="https://ui-avatars.com/api/?name={{ urlencode($user->name) }}&background=7c3aed&color=fff&size=120&bold=true"
                                alt="{{ $user->name }}"
                                class="h-32 w-32 rounded-full object-cover ring-4 ring-brand-500/20">
                            @if ($user->is_active)
                                <div
                                    class="absolute bottom-0 right-0 flex h-8 w-8 items-center justify-center rounded-full bg-emerald-500 ring-2 ring-white">
                                    <svg class="h-4 w-4 text-white" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                            d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                            clip-rule="evenodd" />
                                    </svg>
                                </div>
                            @else
                                <div
                                    class="absolute bottom-0 right-0 flex h-8 w-8 items-center justify-center rounded-full bg-red-500 ring-2 ring-white">
                                    <svg class="h-4 w-4 text-white" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                            d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                                            clip-rule="evenodd" />
                                    </svg>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- User Info -->
                    <div class="space-y-3 text-center">
                        <div>
                            <h2 class="text-xl font-bold text-gray-900 dark:text-white">{{ $user->name }}</h2>
                            <span
                                class="mt-2 inline-block rounded-full bg-brand-50 px-3 py-1 text-xs font-semibold uppercase tracking-[0.15em] text-brand-600 dark:bg-brand-950 dark:text-brand-300">
                                {{ \App\Models\User::ROLES[$user->role] ?? $user->role }}
                            </span>
                        </div>
                    </div>

                    <!-- Status Badge -->
                    <div class="mt-6 border-t border-gray-200 pt-6 dark:border-gray-800">
                        <div class="flex items-center justify-between">
                            <span class="text-sm font-medium text-gray-600 dark:text-gray-400">Status Akun</span>
                            @if ($user->is_active)
                                <span
                                    class="inline-flex items-center gap-1.5 rounded-full bg-emerald-50 px-3 py-1 text-xs font-semibold text-emerald-700 dark:bg-emerald-950 dark:text-emerald-300">
                                    <span class="h-2 w-2 rounded-full bg-emerald-500"></span>
                                    Aktif
                                </span>
                            @else
                                <span
                                    class="inline-flex items-center gap-1.5 rounded-full bg-red-50 px-3 py-1 text-xs font-semibold text-red-700 dark:bg-red-950 dark:text-red-300">
                                    <span class="h-2 w-2 rounded-full bg-red-500"></span>
                                    Nonaktif
                                </span>
                            @endif
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="mt-6 space-y-2 border-t border-gray-200 pt-6 dark:border-gray-800">
                        <button
                            class="flex w-full items-center justify-center gap-2 rounded-lg bg-blue-50 px-4 py-2.5 text-sm font-medium text-blue-600 transition hover:bg-blue-100 dark:bg-blue-950 dark:text-blue-300 dark:hover:bg-blue-900">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                            </svg>
                            Edit User
                        </button>
                        <button
                            onclick="if(confirm('Apakah Anda yakin ingin menghapus user ini?')) { document.getElementById('delete-form').submit(); }"
                            class="flex w-full items-center justify-center gap-2 rounded-lg bg-red-50 px-4 py-2.5 text-sm font-medium text-red-600 transition hover:bg-red-100 dark:bg-red-950 dark:text-red-300 dark:hover:bg-red-900">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                            </svg>
                            Hapus User
                        </button>
                        <form id="delete-form" method="POST" action="{{ route('admin.users.destroy', $user) }}"
                            style="display: none;">
                            @csrf
                            @method('DELETE')
                        </form>
                    </div>
                </div>
            </div>

            <!-- Details Section -->
            <div class="lg:col-span-2">
                <div class="space-y-4">
                    <!-- Contact Information -->
                    <div
                        class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-800 dark:bg-gray-900">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Informasi Kontak</h3>

                        <div class="mt-4 space-y-4">
                            <!-- Email -->
                            <div
                                class="flex items-start gap-3 border-b border-gray-100 pb-4 last:border-0 dark:border-gray-800">
                                <div
                                    class="mt-1 flex h-10 w-10 items-center justify-center rounded-lg bg-blue-50 dark:bg-blue-950">
                                    <svg class="h-5 w-5 text-blue-600 dark:text-blue-300" fill="none"
                                        stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                    </svg>
                                </div>
                                <div class="flex-1">
                                    <p class="text-xs font-medium uppercase tracking-wide text-gray-500 dark:text-gray-400">
                                        Email</p>
                                    <p class="mt-1 break-all text-sm font-semibold text-gray-900 dark:text-white">
                                        {{ $user->email }}</p>
                                </div>
                            </div>

                            <!-- Telegram ID -->
                            <div
                                class="flex items-start gap-3 border-b border-gray-100 pb-4 last:border-0 dark:border-gray-800">
                                <div
                                    class="mt-1 flex h-10 w-10 items-center justify-center rounded-lg bg-cyan-50 dark:bg-cyan-950">
                                    <svg class="h-5 w-5 text-cyan-600 dark:text-cyan-300" fill="currentColor"
                                        viewBox="0 0 24 24">
                                        <path
                                            d="M12 0C5.373 0 0 5.373 0 12s5.373 12 12 12 12-5.373 12-12S18.627 0 12 0zm5.894 8.221l-1.97 9.28c-.145.658-.537.818-1.084.508l-3-2.21-1.446 1.394c-.16.16-.295.295-.605.295-.386 0-.315-.14-.444-.492l-2.04-6.693c-.135-.44-.027-.64.303-.745l13.325-5.139c.61-.28.934.144.77.914z" />
                                    </svg>
                                </div>
                                <div class="flex-1">
                                    <p class="text-xs font-medium uppercase tracking-wide text-gray-500 dark:text-gray-400">
                                        Telegram Chat ID
                                    </p>
                                    @if ($user->telegram_chat_id)
                                        <p class="mt-1 font-mono text-sm font-semibold text-gray-900 dark:text-white">
                                            {{ $user->telegram_chat_id }}
                                        </p>
                                    @else
                                        <p class="mt-1 text-sm text-amber-600 dark:text-amber-400">
                                            Belum diatur
                                        </p>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Account Information -->
                    <div
                        class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-800 dark:bg-gray-900">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Informasi Akun</h3>

                        <div class="mt-4 grid gap-4 sm:grid-cols-2">
                            <!-- Role -->
                            <div
                                class="rounded-lg border border-gray-100 bg-gray-50 p-4 dark:border-gray-800 dark:bg-gray-800">
                                <p class="text-xs font-medium uppercase tracking-wide text-gray-500 dark:text-gray-400">Role
                                </p>
                                <p class="mt-2 text-sm font-semibold text-gray-900 dark:text-white">
                                    {{ \App\Models\User::ROLES[$user->role] ?? $user->role }}
                                </p>
                            </div>

                            <!-- Account Status -->
                            <div
                                class="rounded-lg border border-gray-100 bg-gray-50 p-4 dark:border-gray-800 dark:bg-gray-800">
                                <p class="text-xs font-medium uppercase tracking-wide text-gray-500 dark:text-gray-400">
                                    Status</p>
                                <p class="mt-2">
                                    @if ($user->is_active)
                                        <span
                                            class="text-sm font-semibold text-emerald-600 dark:text-emerald-400">Aktif</span>
                                    @else
                                        <span class="text-sm font-semibold text-red-600 dark:text-red-400">Nonaktif</span>
                                    @endif
                                </p>
                            </div>

                            <!-- Email Verified -->
                            <div
                                class="rounded-lg border border-gray-100 bg-gray-50 p-4 dark:border-gray-800 dark:bg-gray-800">
                                <p class="text-xs font-medium uppercase tracking-wide text-gray-500 dark:text-gray-400">
                                    Email
                                    Verified</p>
                                <p class="mt-2">
                                    @if ($user->email_verified_at)
                                        <span class="text-sm font-semibold text-emerald-600 dark:text-emerald-400">
                                            {{ $user->email_verified_at->format('d M Y, H:i') }}
                                        </span>
                                    @else
                                        <span class="text-sm font-semibold text-amber-600 dark:text-amber-400">Belum
                                            diverifikasi</span>
                                    @endif
                                </p>
                            </div>

                            <!-- Last Login -->
                            <div
                                class="rounded-lg border border-gray-100 bg-gray-50 p-4 dark:border-gray-800 dark:bg-gray-800">
                                <p class="text-xs font-medium uppercase tracking-wide text-gray-500 dark:text-gray-400">
                                    Login
                                    Terakhir</p>
                                <p class="mt-2 text-sm font-semibold text-gray-900 dark:text-white">
                                    @if ($user->last_login_at)
                                        {{ $user->last_login_at->diffForHumans() }}
                                    @else
                                        Belum pernah login
                                    @endif
                                </p>
                            </div>

                            <!-- Joined Date -->
                            <div
                                class="rounded-lg border border-gray-100 bg-gray-50 p-4 dark:border-gray-800 dark:bg-gray-800">
                                <p class="text-xs font-medium uppercase tracking-wide text-gray-500 dark:text-gray-400">
                                    Joined</p>
                                <p class="mt-2 text-sm font-semibold text-gray-900 dark:text-white">
                                    {{ $user->created_at->format('d M Y') }}
                                </p>
                            </div>

                            <!-- Updated Date -->
                            <div
                                class="rounded-lg border border-gray-100 bg-gray-50 p-4 dark:border-gray-800 dark:bg-gray-800">
                                <p class="text-xs font-medium uppercase tracking-wide text-gray-500 dark:text-gray-400">
                                    Last
                                    Updated</p>
                                <p class="mt-2 text-sm font-semibold text-gray-900 dark:text-white">
                                    {{ $user->updated_at->format('d M Y, H:i') }}
                                </p>
                            </div>

                            <!-- Jumlah Token -->
                            <div
                                class="rounded-lg border border-gray-100 bg-gradient-to-br from-brand-50 to-brand-100/50 p-4 dark:border-gray-800 dark:bg-gradient-to-br dark:from-brand-950 dark:to-brand-900/30">
                                <p class="text-xs font-medium uppercase tracking-wide text-brand-600 dark:text-brand-400">
                                    Token Digunakan</p>
                                <p class="mt-2 text-3xl font-bold text-brand-600 dark:text-brand-300">
                                    {{ number_format($user->jumlah_token) }}
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Account ID -->
                    <div
                        class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-800 dark:bg-gray-900">
                        <p class="text-xs font-medium uppercase tracking-wide text-gray-500 dark:text-gray-400">User ID</p>
                        <p class="mt-2 font-mono text-lg font-bold text-gray-900 dark:text-white">{{ $user->id }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="space-y-6">
    {{-- Welcome Section --}}
    <div class="relative overflow-hidden rounded-2xl bg-gradient-to-r from-brand-500 to-brand-600 p-8">
        <div class="relative z-10">
            <h1 class="text-2xl font-bold text-white">Selamat Datang, {{ auth()->user()->name }}!</h1>
            <p class="mt-2 text-brand-100">Kelola penilaian properti dengan sistem tracking yang terintegrasi.</p>
            <div class="mt-6 flex flex-wrap gap-3">
                <a href="{{ route('kanban.dashboard') }}" class="inline-flex items-center gap-2 rounded-lg bg-white/20 backdrop-blur-sm px-4 py-2.5 text-sm font-medium text-white hover:bg-white/30 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                    </svg>
                    Tracker Dashboard
                </a>
                <a href="{{ route('kanban.assets.index') }}" class="inline-flex items-center gap-2 rounded-lg bg-white px-4 py-2.5 text-sm font-medium text-brand-600 hover:bg-brand-50 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17V7m0 10a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2h2a2 2 0 012 2m0 10a2 2 0 002 2h2a2 2 0 002-2M9 7a2 2 0 012-2h2a2 2 0 012 2m0 10V7m0 10a2 2 0 002 2h2a2 2 0 002-2V7a2 2 0 00-2-2h-2a2 2 0 00-2 2" />
                    </svg>
                    Kanban Board
                </a>
            </div>
        </div>
        {{-- Decorative elements --}}
        <div class="absolute -right-10 -top-10 h-40 w-40 rounded-full bg-white/10"></div>
        <div class="absolute -bottom-10 -right-5 h-32 w-32 rounded-full bg-white/10"></div>
        <div class="absolute top-1/2 right-1/4 h-20 w-20 rounded-full bg-white/5"></div>
    </div>

    {{-- Quick Stats --}}
    <div class="grid grid-cols-2 lg:grid-cols-3 gap-4">
        <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-gray-900">
            <div class="flex items-center gap-4">
                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-brand-100 dark:bg-brand-900/30">
                    <svg class="w-6 h-6 text-brand-600 dark:text-brand-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                    </svg>
                </div>
                <div>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ \App\Models\AssetKanban::count() }}</p>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Total Asset</p>
                </div>
            </div>
        </div>

        <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-gray-900">
            <div class="flex items-center gap-4">
                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-purple-100 dark:bg-purple-900/30">
                    <svg class="w-6 h-6 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                </div>
                <div>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ \App\Models\ClientKanban::count() }}</p>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Total Klien</p>
                </div>
            </div>
        </div>

        <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-gray-900">
            <div class="flex items-center gap-4">
                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-emerald-100 dark:bg-emerald-900/30">
                    <svg class="w-6 h-6 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ \App\Models\AssetKanban::where('current_stage', 13)->count() }}</p>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Selesai</p>
                </div>
            </div>
        </div>

    </div>

    {{-- Quick Access --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Menu Cards --}}
        <div class="lg:col-span-2">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Akses Cepat</h2>
            <div class="grid grid-cols-2 sm:grid-cols-3 gap-4">
                <a href="{{ route('kanban.dashboard') }}" class="group flex flex-col items-center p-6 rounded-2xl border border-gray-200 bg-white hover:border-brand-300 hover:shadow-lg transition dark:border-gray-800 dark:bg-gray-900 dark:hover:border-brand-700">
                    <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-brand-400 to-brand-600 flex items-center justify-center mb-4 group-hover:scale-110 transition shadow-lg shadow-brand-500/25">
                        <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                        </svg>
                    </div>
                    <span class="text-sm font-medium text-gray-900 dark:text-white">Tracker</span>
                    <span class="text-xs text-gray-500 dark:text-gray-400 mt-1">Dashboard</span>
                </a>

                <a href="{{ route('kanban.assets.index') }}" class="group flex flex-col items-center p-6 rounded-2xl border border-gray-200 bg-white hover:border-blue-300 hover:shadow-lg transition dark:border-gray-800 dark:bg-gray-900 dark:hover:border-blue-700">
                    <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-blue-400 to-blue-600 flex items-center justify-center mb-4 group-hover:scale-110 transition shadow-lg shadow-blue-500/25">
                        <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17V7m0 10a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2h2a2 2 0 012 2m0 10a2 2 0 002 2h2a2 2 0 002-2M9 7a2 2 0 012-2h2a2 2 0 012 2m0 10V7m0 10a2 2 0 002 2h2a2 2 0 002-2V7a2 2 0 00-2-2h-2a2 2 0 00-2 2" />
                        </svg>
                    </div>
                    <span class="text-sm font-medium text-gray-900 dark:text-white">Kanban</span>
                    <span class="text-xs text-gray-500 dark:text-gray-400 mt-1">Board</span>
                </a>

                <a href="{{ route('kanban.clients.index') }}" class="group flex flex-col items-center p-6 rounded-2xl border border-gray-200 bg-white hover:border-purple-300 hover:shadow-lg transition dark:border-gray-800 dark:bg-gray-900 dark:hover:border-purple-700">
                    <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-purple-400 to-purple-600 flex items-center justify-center mb-4 group-hover:scale-110 transition shadow-lg shadow-purple-500/25">
                        <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                    </div>
                    <span class="text-sm font-medium text-gray-900 dark:text-white">Klien</span>
                    <span class="text-xs text-gray-500 dark:text-gray-400 mt-1">Kelola</span>
                </a>

                <a href="{{ route('kanban.recapitulations.index') }}" class="group flex flex-col items-center p-6 rounded-2xl border border-gray-200 bg-white hover:border-emerald-300 hover:shadow-lg transition dark:border-gray-800 dark:bg-gray-900 dark:hover:border-emerald-700">
                    <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-emerald-400 to-emerald-600 flex items-center justify-center mb-4 group-hover:scale-110 transition shadow-lg shadow-emerald-500/25">
                        <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                    </div>
                    <span class="text-sm font-medium text-gray-900 dark:text-white">Rekapitulasi</span>
                    <span class="text-xs text-gray-500 dark:text-gray-400 mt-1">Laporan</span>
                </a>

                <a href="{{ route('notifications.index') }}" class="group flex flex-col items-center p-6 rounded-2xl border border-gray-200 bg-white hover:border-orange-300 hover:shadow-lg transition dark:border-gray-800 dark:bg-gray-900 dark:hover:border-orange-700">
                    <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-orange-400 to-orange-600 flex items-center justify-center mb-4 group-hover:scale-110 transition shadow-lg shadow-orange-500/25">
                        <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                        </svg>
                    </div>
                    <span class="text-sm font-medium text-gray-900 dark:text-white">Notifikasi</span>
                    <span class="text-xs text-gray-500 dark:text-gray-400 mt-1">Pemberitahuan</span>
                </a>

                <a href="{{ route('assistant.index') }}" class="group flex flex-col items-center p-6 rounded-2xl border border-gray-200 bg-white hover:border-pink-300 hover:shadow-lg transition dark:border-gray-800 dark:bg-gray-900 dark:hover:border-pink-700">
                    <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-pink-400 to-pink-600 flex items-center justify-center mb-4 group-hover:scale-110 transition shadow-lg shadow-pink-500/25">
                        <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z" />
                        </svg>
                    </div>
                    <span class="text-sm font-medium text-gray-900 dark:text-white">AI Assistant</span>
                    <span class="text-xs text-gray-500 dark:text-gray-400 mt-1">Bantuan</span>
                </a>
            </div>
        </div>

        {{-- User Info Card --}}
        <div>
            <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Informasi Akun</h2>
            <div class="rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-gray-900">
                <div class="flex items-center gap-4 mb-6">
                    <div class="w-16 h-16 rounded-full bg-gradient-to-br from-brand-400 to-brand-600 flex items-center justify-center text-white text-2xl font-bold shadow-lg">
                        {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                    </div>
                    <div>
                        <h3 class="font-semibold text-gray-900 dark:text-white">{{ auth()->user()->name }}</h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400">{{ auth()->user()->email }}</p>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-brand-100 text-brand-800 dark:bg-brand-900/30 dark:text-brand-400 mt-1">
                            {{ ucfirst(auth()->user()->role) }}
                        </span>
                    </div>
                </div>

                <div class="space-y-3">
                    <div class="flex items-center justify-between py-2 border-b border-gray-100 dark:border-gray-800">
                        <span class="text-sm text-gray-500 dark:text-gray-400">Status</span>
                        <span class="inline-flex items-center gap-1.5 text-sm font-medium text-emerald-600 dark:text-emerald-400">
                            <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                            Aktif
                        </span>
                    </div>
                    <div class="flex items-center justify-between py-2 border-b border-gray-100 dark:border-gray-800">
                        <span class="text-sm text-gray-500 dark:text-gray-400">Telegram</span>
                        @if(auth()->user()->telegram_chat_id)
                            <span class="text-sm font-medium text-emerald-600 dark:text-emerald-400">Terhubung</span>
                        @else
                            <span class="text-sm font-medium text-gray-400">Belum terhubung</span>
                        @endif
                    </div>
                    <div class="flex items-center justify-between py-2">
                        <span class="text-sm text-gray-500 dark:text-gray-400">Last Login</span>
                        <span class="text-sm text-gray-900 dark:text-white">{{ auth()->user()->last_login_at?->diffForHumans() ?? 'Baru saja' }}</span>
                    </div>
                </div>

                <a href="{{ route('profile.edit') }}" class="mt-6 w-full inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-lg border border-gray-300 dark:border-gray-700 text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800 transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                    </svg>
                    Edit Profile
                </a>
            </div>
        </div>
    </div>
</div>
@endsection

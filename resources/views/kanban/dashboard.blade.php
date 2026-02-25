@extends('layouts.app')

@section('title', 'Dashboard Kanban')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Dashboard Kanban</h1>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Overview pekerjaan penilaian properti</p>
        </div>
        <div class="flex gap-2">
            @if(auth()->user()->hasAdminAccess())
            <a href="{{ route('kanban.projects.create') }}" 
               class="inline-flex items-center justify-center gap-2 rounded-lg bg-brand-500 px-4 py-2.5 text-sm font-medium text-white hover:bg-brand-600 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Proyek Baru
            </a>
            @endif
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
        <!-- Total Projects -->
        <div class="rounded-xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-gray-900">
            <div class="flex items-center gap-4">
                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-brand-50 dark:bg-brand-500/10">
                    <svg class="w-6 h-6 text-brand-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                </div>
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Total Proyek</p>
                    <h3 class="text-2xl font-bold text-gray-900 dark:text-white">{{ $stats['total_projects'] }}</h3>
                </div>
            </div>
        </div>

        <!-- Active Projects -->
        <div class="rounded-xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-gray-900">
            <div class="flex items-center gap-4">
                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-blue-50 dark:bg-blue-500/10">
                    <svg class="w-6 h-6 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                    </svg>
                </div>
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Proyek Aktif</p>
                    <h3 class="text-2xl font-bold text-gray-900 dark:text-white">{{ $stats['active_projects'] }}</h3>
                </div>
            </div>
        </div>

        <!-- Total Assets -->
        <div class="rounded-xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-gray-900">
            <div class="flex items-center gap-4">
                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-green-50 dark:bg-green-500/10">
                    <svg class="w-6 h-6 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                    </svg>
                </div>
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Total Asset</p>
                    <h3 class="text-2xl font-bold text-gray-900 dark:text-white">{{ $stats['total_assets'] }}</h3>
                </div>
            </div>
        </div>

        <!-- Total Clients -->
        <div class="rounded-xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-gray-900">
            <div class="flex items-center gap-4">
                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-purple-50 dark:bg-purple-500/10">
                    <svg class="w-6 h-6 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                </div>
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Total Klien</p>
                    <h3 class="text-2xl font-bold text-gray-900 dark:text-white">{{ $stats['total_clients'] }}</h3>
                </div>
            </div>
        </div>
    </div>

    <!-- Assets by Stage -->
    <div class="rounded-xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-gray-900">
        <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Asset per Stage</h2>
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 xl:grid-cols-7 gap-3">
            @php
                $stages = \App\Models\ProjectAssetKanban::STAGES;
                $stageColors = [
                    1 => 'blue', 2 => 'blue', 3 => 'blue', 4 => 'blue',
                    5 => 'blue', 6 => 'blue', 7 => 'blue', 8 => 'blue',
                    9 => 'blue', 10 => 'blue', 11 => 'blue', 12 => 'blue', 13 => 'blue'
                ];
            @endphp
            @foreach($stages as $stageNum => $stageName)
                @php $color = $stageColors[$stageNum] ?? 'gray'; @endphp
                <div class="rounded-lg border border-{{ $color }}-200 dark:border-{{ $color }}-800/50 bg-{{ $color }}-50/50 dark:bg-{{ $color }}-900/20 p-3 text-center">
                    <p class="text-xs text-{{ $color }}-600 dark:text-{{ $color }}-400 truncate" title="{{ $stageName }}">{{ Str::limit($stageName, 12) }}</p>
                    <p class="text-xl font-bold text-{{ $color }}-700 dark:text-{{ $color }}-300">{{ $stats['assets_by_stage'][$stageNum] ?? 0 }}</p>
                </div>
            @endforeach
        </div>
    </div>

    <!-- Priority Assets -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Critical Priority -->
        <div class="rounded-xl border border-red-200 bg-white dark:border-red-800/50 dark:bg-gray-900 overflow-hidden">
            <div class="px-5 py-4 border-b border-red-200 dark:border-red-800/50 bg-red-50 dark:bg-red-900/20">
                <h3 class="font-semibold text-red-700 dark:text-red-400 flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                    Prioritas Kritikal ({{ $stats['critical_count'] ?? 0 }})
                </h3>
            </div>
            <div class="p-4 max-h-64 overflow-y-auto">
                @forelse($criticalAssets ?? [] as $asset)
                <a href="{{ route('kanban.assets.show', $asset) }}" class="block p-3 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-800 transition mb-2">
                    <div class="flex justify-between items-start">
                        <div>
                            <p class="font-medium text-gray-900 dark:text-white">{{ $asset->name }}</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">{{ $asset->asset_code }} • {{ $asset->project->name ?? '-' }}</p>
                        </div>
                        <span class="text-xs bg-red-100 text-red-700 dark:bg-red-900/50 dark:text-red-400 px-2 py-1 rounded">
                            {{ $asset->stage_label }}
                        </span>
                    </div>
                </a>
                @empty
                <p class="text-sm text-gray-500 dark:text-gray-400 text-center py-4">Tidak ada asset dengan prioritas kritikal</p>
                @endforelse
            </div>
        </div>

        <!-- Recent Activities -->
        <div class="rounded-xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900 overflow-hidden">
            <div class="px-5 py-4 border-b border-gray-200 dark:border-gray-800">
                <h3 class="font-semibold text-gray-900 dark:text-white flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    Aktivitas Terbaru
                </h3>
            </div>
            <div class="p-4 max-h-64 overflow-y-auto">
                @forelse($recentActivities ?? [] as $activity)
                <div class="flex gap-3 mb-3 pb-3 border-b border-gray-100 dark:border-gray-800 last:border-0">
                    <div class="flex-shrink-0 w-8 h-8 rounded-full bg-gray-100 dark:bg-gray-800 flex items-center justify-center">
                        <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                        </svg>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm text-gray-900 dark:text-white">{{ $activity->content }}</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400">{{ $activity->user->name ?? 'System' }} • {{ $activity->created_at->diffForHumans() }}</p>
                    </div>
                </div>
                @empty
                <p class="text-sm text-gray-500 dark:text-gray-400 text-center py-4">Belum ada aktivitas</p>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Quick Links -->
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
        <a href="{{ route('kanban.clients.index') }}" class="flex items-center gap-3 p-4 rounded-xl border border-gray-200 bg-white hover:border-brand-300 hover:shadow-md transition dark:border-gray-800 dark:bg-gray-900 dark:hover:border-brand-600">
            <div class="p-2 rounded-lg bg-purple-100 dark:bg-purple-900/30">
                <svg class="w-6 h-6 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                </svg>
            </div>
            <div>
                <p class="font-medium text-gray-900 dark:text-white">Klien</p>
                <p class="text-xs text-gray-500 dark:text-gray-400">Kelola klien</p>
            </div>
        </a>
        <a href="{{ route('kanban.projects.index') }}" class="flex items-center gap-3 p-4 rounded-xl border border-gray-200 bg-white hover:border-brand-300 hover:shadow-md transition dark:border-gray-800 dark:bg-gray-900 dark:hover:border-brand-600">
            <div class="p-2 rounded-lg bg-blue-100 dark:bg-blue-900/30">
                <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
            </div>
            <div>
                <p class="font-medium text-gray-900 dark:text-white">Proyek</p>
                <p class="text-xs text-gray-500 dark:text-gray-400">Kelola proyek</p>
            </div>
        </a>
        <a href="{{ route('kanban.assets.index') }}" class="flex items-center gap-3 p-4 rounded-xl border border-gray-200 bg-white hover:border-brand-300 hover:shadow-md transition dark:border-gray-800 dark:bg-gray-900 dark:hover:border-brand-600">
            <div class="p-2 rounded-lg bg-green-100 dark:bg-green-900/30">
                <svg class="w-6 h-6 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                </svg>
            </div>
            <div>
                <p class="font-medium text-gray-900 dark:text-white">Asset</p>
                <p class="text-xs text-gray-500 dark:text-gray-400">Objek penilaian</p>
            </div>
        </a>
        @if(auth()->user()->hasAdminAccess())
        <a href="{{ route('admin.reports') }}" class="flex items-center gap-3 p-4 rounded-xl border border-gray-200 bg-white hover:border-brand-300 hover:shadow-md transition dark:border-gray-800 dark:bg-gray-900 dark:hover:border-brand-600">
            <div class="p-2 rounded-lg bg-orange-100 dark:bg-orange-900/30">
                <svg class="w-6 h-6 text-orange-600 dark:text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
            </div>
            <div>
                <p class="font-medium text-gray-900 dark:text-white">Laporan</p>
                <p class="text-xs text-gray-500 dark:text-gray-400">Admin only</p>
            </div>
        </a>
        @endif
    </div>
</div>
@endsection

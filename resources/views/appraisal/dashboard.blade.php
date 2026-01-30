@extends('layouts.app')

@section('title', 'Dashboard Appraisal')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Dashboard Appraisal</h1>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Overview pekerjaan penilaian properti</p>
        </div>
        <a href="{{ route('appraisal.projects.create') }}" 
           class="inline-flex items-center justify-center gap-2 rounded-lg bg-brand-500 px-4 py-2.5 text-sm font-medium text-white hover:bg-brand-600 transition-colors">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            Proyek Baru
        </a>
    </div>

    <!-- Project Stats (Administrative Level) -->
    <div>
        <h2 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-3 flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
            </svg>
            Level Proyek (Administrasi)
        </h2>
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

            <!-- Completed This Month -->
            <div class="rounded-xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-gray-900">
                <div class="flex items-center gap-4">
                    <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-green-50 dark:bg-green-500/10">
                        <svg class="w-6 h-6 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Selesai Bulan Ini</p>
                        <h3 class="text-2xl font-bold text-gray-900 dark:text-white">{{ $stats['completed_this_month'] }}</h3>
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
    </div>

    <!-- Asset Stats (Technical Level) -->
    <div>
        <h2 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-3 flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
            </svg>
            Level Objek Penilaian (Teknis)
        </h2>
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
            <!-- Total Assets -->
            <div class="rounded-xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-gray-900">
                <div class="flex items-center gap-4">
                    <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-indigo-50 dark:bg-indigo-500/10">
                        <svg class="w-6 h-6 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Total Objek</p>
                        <h3 class="text-2xl font-bold text-gray-900 dark:text-white">{{ $stats['total_assets'] ?? 0 }}</h3>
                    </div>
                </div>
            </div>

            <!-- Active Assets -->
            <div class="rounded-xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-gray-900">
                <div class="flex items-center gap-4">
                    <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-cyan-50 dark:bg-cyan-500/10">
                        <svg class="w-6 h-6 text-cyan-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Objek Aktif</p>
                        <h3 class="text-2xl font-bold text-gray-900 dark:text-white">{{ $stats['active_assets'] ?? 0 }}</h3>
                    </div>
                </div>
            </div>

            <!-- Completed Assets This Month -->
            <div class="rounded-xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-gray-900">
                <div class="flex items-center gap-4">
                    <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-teal-50 dark:bg-teal-500/10">
                        <svg class="w-6 h-6 text-teal-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Objek Selesai Bulan Ini</p>
                        <h3 class="text-2xl font-bold text-gray-900 dark:text-white">{{ $stats['assets_completed_this_month'] ?? 0 }}</h3>
                    </div>
                </div>
            </div>

            <!-- Invoice Status -->
            <div class="rounded-xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-gray-900">
                <div class="flex items-center gap-4">
                    <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-orange-50 dark:bg-orange-500/10">
                        <svg class="w-6 h-6 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Invoice Belum Dibayar</p>
                        <h3 class="text-2xl font-bold text-gray-900 dark:text-white">{{ $stats['unpaid_invoices'] ?? 0 }}</h3>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Priority Alerts -->
    @if($priorityStats['warning'] > 0 || $priorityStats['critical'] > 0)
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
        @if($priorityStats['critical'] > 0)
        <div class="flex items-center gap-4 rounded-xl border border-red-200 bg-red-50 p-4 dark:border-red-800 dark:bg-red-900/20">
            <div class="flex h-10 w-10 items-center justify-center rounded-full bg-red-100 dark:bg-red-900/50">
                <svg class="w-5 h-5 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
            </div>
            <div>
                <p class="font-semibold text-red-800 dark:text-red-200">{{ $priorityStats['critical'] }} Proyek Kritis</p>
                <p class="text-sm text-red-600 dark:text-red-400">Perlu perhatian segera!</p>
            </div>
        </div>
        @endif

        @if($priorityStats['warning'] > 0)
        <div class="flex items-center gap-4 rounded-xl border border-yellow-200 bg-yellow-50 p-4 dark:border-yellow-800 dark:bg-yellow-900/20">
            <div class="flex h-10 w-10 items-center justify-center rounded-full bg-yellow-100 dark:bg-yellow-900/50">
                <svg class="w-5 h-5 text-yellow-600 dark:text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
            <div>
                <p class="font-semibold text-yellow-800 dark:text-yellow-200">{{ $priorityStats['warning'] }} Proyek Perlu Perhatian</p>
                <p class="text-sm text-yellow-600 dark:text-yellow-400">Ada kendala yang perlu ditangani</p>
            </div>
        </div>
        @endif
    </div>
    @endif

    <!-- Workflow Pipelines -->
    <div class="grid grid-cols-1 gap-6 xl:grid-cols-2">
        <!-- Project Pipeline (Administrative Level) -->
        <div class="rounded-xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900">
            <div class="border-b border-gray-200 px-6 py-4 dark:border-gray-800">
                <div class="flex items-center justify-between">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white flex items-center gap-2">
                        <svg class="w-5 h-5 text-brand-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        Pipeline Proyek
                    </h2>
                    <a href="{{ route('appraisal.projects.index') }}" class="text-sm text-brand-500 hover:text-brand-600">
                        Lihat Kanban →
                    </a>
                </div>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Level administrasi (1 proyek = 1 proposal)</p>
            </div>
            <div class="p-6">
                <div class="flex gap-3 overflow-x-auto pb-2">
                    @foreach($projectsByStage as $stage => $data)
                    <div class="flex-shrink-0 w-24">
                        <div class="text-center">
                            <div class="mx-auto mb-2 flex h-10 w-10 items-center justify-center rounded-xl 
                                @if($stage === 'done') bg-green-100 dark:bg-green-900/30
                                @elseif(in_array($stage, ['lead', 'proposal'])) bg-blue-100 dark:bg-blue-900/30
                                @elseif(in_array($stage, ['contract', 'invoice'])) bg-yellow-100 dark:bg-yellow-900/30
                                @else bg-gray-100 dark:bg-gray-800
                                @endif">
                                <span class="text-base font-bold 
                                    @if($stage === 'done') text-green-600 dark:text-green-400
                                    @elseif(in_array($stage, ['lead', 'proposal'])) text-blue-600 dark:text-blue-400
                                    @elseif(in_array($stage, ['contract', 'invoice'])) text-yellow-600 dark:text-yellow-400
                                    @else text-gray-600 dark:text-gray-400
                                    @endif">
                                    {{ $data['count'] }}
                                </span>
                            </div>
                            <p class="text-xs font-medium text-gray-600 dark:text-gray-400 leading-tight">{{ $data['label'] }}</p>
                        </div>
                    </div>
                    @if(!$loop->last)
                    <div class="flex items-center justify-center flex-shrink-0 pt-1">
                        <svg class="w-3 h-3 text-gray-300 dark:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </div>
                    @endif
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Asset Pipeline (Technical Level) -->
        <div class="rounded-xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900">
            <div class="border-b border-gray-200 px-6 py-4 dark:border-gray-800">
                <div class="flex items-center justify-between">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white flex items-center gap-2">
                        <svg class="w-5 h-5 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                        </svg>
                        Pipeline Objek Penilaian
                    </h2>
                    <a href="{{ route('appraisal.assets.index') }}" class="text-sm text-indigo-500 hover:text-indigo-600">
                        Lihat Kanban →
                    </a>
                </div>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Level teknis (1 proyek = banyak objek)</p>
            </div>
            <div class="p-6">
                @if(!empty($assetsByStage))
                <div class="flex gap-3 overflow-x-auto pb-2">
                    @foreach($assetsByStage as $stage => $data)
                    <div class="flex-shrink-0 w-24">
                        <div class="text-center">
                            <div class="mx-auto mb-2 flex h-10 w-10 items-center justify-center rounded-xl 
                                @if($stage === 'done') bg-green-100 dark:bg-green-900/30
                                @elseif(in_array($stage, ['inspection', 'data_collection'])) bg-blue-100 dark:bg-blue-900/30
                                @elseif(in_array($stage, ['analysis', 'review'])) bg-purple-100 dark:bg-purple-900/30
                                @else bg-gray-100 dark:bg-gray-800
                                @endif">
                                <span class="text-base font-bold 
                                    @if($stage === 'done') text-green-600 dark:text-green-400
                                    @elseif(in_array($stage, ['inspection', 'data_collection'])) text-blue-600 dark:text-blue-400
                                    @elseif(in_array($stage, ['analysis', 'review'])) text-purple-600 dark:text-purple-400
                                    @else text-gray-600 dark:text-gray-400
                                    @endif">
                                    {{ $data['count'] }}
                                </span>
                            </div>
                            <p class="text-xs font-medium text-gray-600 dark:text-gray-400 leading-tight">{{ $data['label'] }}</p>
                        </div>
                    </div>
                    @if(!$loop->last)
                    <div class="flex items-center justify-center flex-shrink-0 pt-1">
                        <svg class="w-3 h-3 text-gray-300 dark:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </div>
                    @endif
                    @endforeach
                </div>
                @else
                <div class="text-center py-6">
                    <svg class="mx-auto h-10 w-10 text-gray-300 dark:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                    </svg>
                    <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">Belum ada objek penilaian</p>
                </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Main Content Grid -->
    <div class="grid grid-cols-1 gap-6 xl:grid-cols-2 2xl:grid-cols-4">
        <!-- Overdue Projects -->
        <div class="rounded-xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900">
            <div class="border-b border-gray-200 px-6 py-4 dark:border-gray-800">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white flex items-center gap-2">
                    <svg class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    Proyek Terlambat
                </h2>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Deadline proyek sudah lewat</p>
            </div>
            <div class="divide-y divide-gray-200 dark:divide-gray-800 max-h-80 overflow-y-auto">
                @forelse($overdueProjects as $project)
                <a href="{{ route('appraisal.projects.show', $project) }}" class="block px-6 py-4 hover:bg-gray-50 dark:hover:bg-gray-800/50 transition-colors">
                    <div class="flex items-start justify-between gap-3">
                        <div class="min-w-0">
                            <p class="font-medium text-gray-900 dark:text-white truncate">{{ $project->name }}</p>
                            <p class="text-sm text-gray-500 dark:text-gray-400">{{ $project->client->name ?? 'No Client' }}</p>
                            <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">{{ $project->total_assets ?? 0 }} objek</p>
                        </div>
                        <span class="flex-shrink-0 inline-flex items-center rounded-full bg-red-100 px-2.5 py-0.5 text-xs font-medium text-red-800 dark:bg-red-900/30 dark:text-red-400">
                            {{ $project->due_date->diffForHumans() }}
                        </span>
                    </div>
                </a>
                @empty
                <div class="px-6 py-8 text-center">
                    <svg class="mx-auto h-12 w-12 text-gray-300 dark:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">Tidak ada proyek terlambat</p>
                </div>
                @endforelse
            </div>
        </div>

        <!-- Overdue Assets (Objek Penilaian) -->
        <div class="rounded-xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900">
            <div class="border-b border-gray-200 px-6 py-4 dark:border-gray-800">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white flex items-center gap-2">
                    <svg class="w-5 h-5 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                    </svg>
                    Objek Terlambat
                </h2>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Deadline objek penilaian sudah lewat</p>
            </div>
            <div class="divide-y divide-gray-200 dark:divide-gray-800 max-h-80 overflow-y-auto">
                @forelse($overdueAssets ?? [] as $asset)
                <a href="{{ route('appraisal.assets.show', $asset) }}" class="block px-6 py-4 hover:bg-gray-50 dark:hover:bg-gray-800/50 transition-colors">
                    <div class="flex items-start justify-between gap-3">
                        <div class="min-w-0">
                            <p class="font-medium text-gray-900 dark:text-white truncate">{{ $asset->name }}</p>
                            <p class="text-sm text-gray-500 dark:text-gray-400">{{ $asset->project->name ?? 'No Project' }}</p>
                            <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">{{ $asset->asset_code }}</p>
                        </div>
                        <span class="flex-shrink-0 inline-flex items-center rounded-full bg-orange-100 px-2.5 py-0.5 text-xs font-medium text-orange-800 dark:bg-orange-900/30 dark:text-orange-400">
                            {{ $asset->target_completion_date->diffForHumans() }}
                        </span>
                    </div>
                </a>
                @empty
                <div class="px-6 py-8 text-center">
                    <svg class="mx-auto h-12 w-12 text-gray-300 dark:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">Tidak ada objek terlambat</p>
                </div>
                @endforelse
            </div>
        </div>

        <!-- Overdue Invoices -->
        <div class="rounded-xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900">
            <div class="border-b border-gray-200 px-6 py-4 dark:border-gray-800">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white flex items-center gap-2">
                    <svg class="w-5 h-5 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                    </svg>
                    Invoice Jatuh Tempo
                </h2>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Pembayaran yang belum diterima</p>
            </div>
            <div class="divide-y divide-gray-200 dark:divide-gray-800 max-h-80 overflow-y-auto">
                @forelse($overdueInvoices as $invoice)
                <a href="{{ route('appraisal.invoices.show', $invoice) }}" class="block px-6 py-4 hover:bg-gray-50 dark:hover:bg-gray-800/50 transition-colors">
                    <div class="flex items-start justify-between gap-3">
                        <div class="min-w-0">
                            <p class="font-medium text-gray-900 dark:text-white">{{ $invoice->invoice_number }}</p>
                            <p class="text-sm text-gray-500 dark:text-gray-400">{{ $invoice->project->client->name ?? 'No Client' }}</p>
                        </div>
                        <span class="flex-shrink-0 inline-flex items-center rounded-full bg-amber-100 px-2.5 py-0.5 text-xs font-medium text-amber-800 dark:bg-amber-900/30 dark:text-amber-400">
                            {{ $invoice->payment_due_date->diffForHumans() }}
                        </span>
                    </div>
                </a>
                @empty
                <div class="px-6 py-8 text-center">
                    <svg class="mx-auto h-12 w-12 text-gray-300 dark:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">Tidak ada invoice jatuh tempo</p>
                </div>
                @endforelse
            </div>
        </div>

        <!-- Recent Activities -->
        <div class="rounded-xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900">
            <div class="border-b border-gray-200 px-6 py-4 dark:border-gray-800">
                <div class="flex items-center justify-between">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white flex items-center gap-2">
                        <svg class="w-5 h-5 text-brand-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        Aktivitas Terbaru
                    </h2>
                    <a href="{{ route('appraisal.activities.index') }}" class="text-sm text-brand-500 hover:text-brand-600">
                        Lihat Semua
                    </a>
                </div>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Proyek & objek penilaian</p>
            </div>
            <div class="divide-y divide-gray-200 dark:divide-gray-800 max-h-80 overflow-y-auto">
                @forelse($recentActivities as $activity)
                <div class="px-6 py-4">
                    <div class="flex items-start gap-3">
                        <div class="flex-shrink-0">
                            @switch($activity->activity_type)
                                @case('stage_move')
                                    <div class="flex h-8 w-8 items-center justify-center rounded-full bg-blue-100 dark:bg-blue-900/30">
                                        <svg class="w-4 h-4 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6" />
                                        </svg>
                                    </div>
                                    @break
                                @case('comment')
                                    <div class="flex h-8 w-8 items-center justify-center rounded-full bg-gray-100 dark:bg-gray-800">
                                        <svg class="w-4 h-4 text-gray-600 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                                        </svg>
                                    </div>
                                    @break
                                @case('approval')
                                    <div class="flex h-8 w-8 items-center justify-center rounded-full bg-green-100 dark:bg-green-900/30">
                                        <svg class="w-4 h-4 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                        </svg>
                                    </div>
                                    @break
                                @case('obstacle')
                                    <div class="flex h-8 w-8 items-center justify-center rounded-full bg-red-100 dark:bg-red-900/30">
                                        <svg class="w-4 h-4 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                        </svg>
                                    </div>
                                    @break
                                @case('asset_added')
                                    <div class="flex h-8 w-8 items-center justify-center rounded-full bg-indigo-100 dark:bg-indigo-900/30">
                                        <svg class="w-4 h-4 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                        </svg>
                                    </div>
                                    @break
                                @default
                                    <div class="flex h-8 w-8 items-center justify-center rounded-full bg-gray-100 dark:bg-gray-800">
                                        <svg class="w-4 h-4 text-gray-600 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                    </div>
                            @endswitch
                        </div>
                        <div class="min-w-0 flex-1">
                            <p class="text-sm text-gray-900 dark:text-white">{{ $activity->description }}</p>
                            @if($activity->asset)
                            <p class="text-xs text-indigo-500 dark:text-indigo-400 mt-0.5">Objek: {{ $activity->asset->name }}</p>
                            @endif
                            <div class="mt-1 flex items-center gap-2 text-xs text-gray-500 dark:text-gray-400">
                                <span>{{ $activity->user->name ?? 'System' }}</span>
                                <span>•</span>
                                <span>{{ $activity->created_at->diffForHumans() }}</span>
                            </div>
                        </div>
                    </div>
                </div>
                @empty
                <div class="px-6 py-8 text-center">
                    <p class="text-sm text-gray-500 dark:text-gray-400">Belum ada aktivitas</p>
                </div>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection

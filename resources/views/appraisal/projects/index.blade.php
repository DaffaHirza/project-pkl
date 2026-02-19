@extends('layouts.app')

@section('title', 'Daftar Proyek Penilaian')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Daftar Proyek</h1>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Kelola proyek penilaian properti</p>
        </div>
        <div class="flex items-center gap-3">
            <!-- Filter by Status -->
            <form method="GET" action="{{ route('appraisal.projects.index') }}" class="flex items-center gap-3">
                <select name="status" onchange="this.form.submit()"
                        class="rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm dark:border-gray-700 dark:bg-gray-800 dark:text-white">
                    <option value="">Semua Status</option>
                    @foreach($statuses as $key => $label)
                    <option value="{{ $key }}" {{ request('status') === $key ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>

                <!-- Search -->
                <div class="relative">
                    <input type="text" name="search" value="{{ request('search') }}"
                           placeholder="Cari proyek..."
                           class="w-64 rounded-lg border border-gray-300 bg-white pl-10 pr-4 py-2 text-sm dark:border-gray-700 dark:bg-gray-800 dark:text-white">
                    <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </div>
            </form>

            <!-- Toggle View -->
            <div class="flex items-center rounded-lg border border-gray-300 dark:border-gray-700">
                <a href="{{ route('appraisal.projects.index') }}" 
                   class="px-3 py-2 text-sm font-medium rounded-l-lg bg-brand-500 text-white">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
                    </svg>
                </a>
                <a href="{{ route('appraisal.projects.list') }}" 
                   class="px-3 py-2 text-sm font-medium rounded-r-lg text-gray-600 hover:bg-gray-100 dark:text-gray-400 dark:hover:bg-gray-800">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16" />
                    </svg>
                </a>
            </div>

            <!-- New Project Button -->
            <a href="{{ route('appraisal.projects.create') }}" 
               class="inline-flex items-center gap-2 rounded-lg bg-brand-500 px-4 py-2 text-sm font-medium text-white hover:bg-brand-600 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Proyek Baru
            </a>
        </div>
    </div>

    <!-- Status Overview Cards -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        @foreach($statuses as $statusKey => $statusLabel)
        @php
            $count = $projects->where('status', $statusKey)->count();
            $colorClasses = match($statusKey) {
                'ongoing' => 'bg-blue-50 dark:bg-blue-900/20 border-blue-200 dark:border-blue-800',
                'completed' => 'bg-green-50 dark:bg-green-900/20 border-green-200 dark:border-green-800',
                'on_hold' => 'bg-yellow-50 dark:bg-yellow-900/20 border-yellow-200 dark:border-yellow-800',
                'cancelled' => 'bg-red-50 dark:bg-red-900/20 border-red-200 dark:border-red-800',
                default => 'bg-gray-50 dark:bg-gray-800 border-gray-200 dark:border-gray-700',
            };
            $textColor = match($statusKey) {
                'ongoing' => 'text-blue-700 dark:text-blue-400',
                'completed' => 'text-green-700 dark:text-green-400',
                'on_hold' => 'text-yellow-700 dark:text-yellow-400',
                'cancelled' => 'text-red-700 dark:text-red-400',
                default => 'text-gray-700 dark:text-gray-400',
            };
        @endphp
        <a href="{{ route('appraisal.projects.index', ['status' => $statusKey]) }}" 
           class="rounded-xl border p-4 {{ $colorClasses }} hover:shadow-md transition-shadow">
            <div class="text-2xl font-bold {{ $textColor }}">{{ $count }}</div>
            <div class="text-sm {{ $textColor }}">{{ $statusLabel }}</div>
        </a>
        @endforeach
    </div>

    <!-- Projects Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
        @forelse($projects as $project)
        <a href="{{ route('appraisal.projects.show', $project) }}" 
           class="block rounded-xl border border-gray-200 bg-white p-5 hover:shadow-lg transition-shadow dark:border-gray-800 dark:bg-gray-900">
            <div class="flex items-start justify-between mb-3">
                <div>
                    <span class="text-xs font-medium text-brand-500 dark:text-brand-400">{{ $project->project_code }}</span>
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mt-1 line-clamp-1">{{ $project->name }}</h3>
                </div>
                @php
                    $statusBadge = match($project->status) {
                        'ongoing' => 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400',
                        'completed' => 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400',
                        'on_hold' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-400',
                        'cancelled' => 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400',
                        default => 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300',
                    };
                @endphp
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $statusBadge }}">
                    {{ $statuses[$project->status] ?? $project->status }}
                </span>
            </div>

            @if($project->client)
            <div class="flex items-center gap-2 text-sm text-gray-500 dark:text-gray-400 mb-3">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                </svg>
                {{ $project->client->company_name ?? $project->client->name }}
            </div>
            @endif

            @if($project->description)
            <p class="text-sm text-gray-600 dark:text-gray-400 line-clamp-2 mb-4">{{ $project->description }}</p>
            @endif

            <div class="flex items-center justify-between pt-3 border-t border-gray-100 dark:border-gray-800">
                <div class="flex items-center gap-4 text-xs text-gray-500 dark:text-gray-400">
                    <div class="flex items-center gap-1">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5" />
                        </svg>
                        {{ $project->assets->count() }} Objek
                    </div>
                    @if($project->due_date)
                    <div class="flex items-center gap-1 {{ $project->due_date < now() && $project->status === 'ongoing' ? 'text-red-500' : '' }}">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                        {{ $project->due_date->format('d M Y') }}
                    </div>
                    @endif
                </div>
            </div>
        </a>
        @empty
        <div class="col-span-full text-center py-12 text-gray-500 dark:text-gray-400">
            <svg class="mx-auto h-12 w-12 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
            </svg>
            <p class="text-lg font-medium">Tidak ada proyek</p>
            <p class="mt-1">Buat proyek baru untuk memulai</p>
            <a href="{{ route('appraisal.projects.create') }}" 
               class="inline-flex items-center gap-2 mt-4 px-4 py-2 rounded-lg bg-brand-500 text-white hover:bg-brand-600">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Buat Proyek Baru
            </a>
        </div>
        @endforelse
    </div>
</div>
@endsection

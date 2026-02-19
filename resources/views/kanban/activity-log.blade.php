@extends('layouts.app')

@section('title', 'Log Aktivitas')

@section('content')
<div class="mx-auto max-w-7xl">
    <!-- Page Title -->
    <div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h2 class="text-2xl font-bold text-gray-800 dark:text-white">
                Log Aktivitas
            </h2>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                Riwayat semua aktivitas penilaian
            </p>
        </div>
    </div>

    <!-- Filters -->
    <div class="rounded-xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900 p-4 mb-6">
        <form method="GET" action="{{ route('kanban.activity-log') }}" class="flex flex-wrap gap-4 items-end">
            <div class="flex-1 min-w-[200px]">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Cari</label>
                <input type="text" name="search" value="{{ request('search') }}" 
                    placeholder="Cari aktivitas, aset, atau user..."
                    class="w-full rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 px-4 py-2 text-sm focus:border-brand-500 focus:ring-brand-500 dark:text-white">
            </div>
            
            <div class="w-40">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Tipe</label>
                <select name="type" class="w-full rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 px-3 py-2 text-sm focus:border-brand-500 focus:ring-brand-500 dark:text-white">
                    <option value="">Semua Tipe</option>
                    @foreach($types as $key => $label)
                        <option value="{{ $key }}" {{ request('type') == $key ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            
            <div class="w-48">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Stage</label>
                <select name="stage" class="w-full rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 px-3 py-2 text-sm focus:border-brand-500 focus:ring-brand-500 dark:text-white">
                    <option value="">Semua Stage</option>
                    @foreach($stages as $num => $label)
                        <option value="{{ $num }}" {{ request('stage') == $num ? 'selected' : '' }}>{{ $num }}. {{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                
                <div class="w-36">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Dari</label>
                    <input type="date" name="from" value="{{ request('from') }}" 
                        class="w-full rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 px-3 py-2 text-sm focus:border-brand-500 focus:ring-brand-500 dark:text-white">
                </div>
                
                <div class="w-36">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Sampai</label>
                    <input type="date" name="to" value="{{ request('to') }}" 
                        class="w-full rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 px-3 py-2 text-sm focus:border-brand-500 focus:ring-brand-500 dark:text-white">
                </div>
                
                <div class="flex gap-2">
                    <button type="submit" class="inline-flex items-center gap-2 rounded-lg bg-brand-500 px-4 py-2 text-sm font-medium text-white hover:bg-brand-600">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                        Filter
                    </button>
                    <a href="{{ route('kanban.activity-log') }}" class="inline-flex items-center gap-2 rounded-lg border border-gray-300 dark:border-gray-700 px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800">
                        Reset
                    </a>
                </div>
            </form>
        </div>

        <!-- Activity List -->
        <div class="rounded-xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900 overflow-hidden">
            @if($activities->isEmpty())
                <div class="p-8 text-center">
                    <svg class="w-12 h-12 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                    </svg>
                    <p class="text-gray-500 dark:text-gray-400">Tidak ada aktivitas ditemukan</p>
                </div>
            @else
                <div class="divide-y divide-gray-200 dark:divide-gray-800">
                    @foreach($activities as $activity)
                        <div class="p-4 hover:bg-gray-50 dark:hover:bg-gray-800/50 transition">
                            <div class="flex items-start gap-4">
                                <!-- Type Icon -->
                                <div class="flex-shrink-0 mt-1">
                                    @switch($activity->type)
                                        @case('stage_change')
                                            <div class="w-8 h-8 rounded-full bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center">
                                                <svg class="w-4 h-4 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 5l7 7-7 7M5 5l7 7-7 7"/>
                                                </svg>
                                            </div>
                                            @break
                                        @case('approval')
                                            <div class="w-8 h-8 rounded-full bg-green-100 dark:bg-green-900/30 flex items-center justify-center">
                                                <svg class="w-4 h-4 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                                </svg>
                                            </div>
                                            @break
                                        @case('rejection')
                                            <div class="w-8 h-8 rounded-full bg-red-100 dark:bg-red-900/30 flex items-center justify-center">
                                                <svg class="w-4 h-4 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                                </svg>
                                            </div>
                                            @break
                                        @default
                                            <div class="w-8 h-8 rounded-full bg-gray-100 dark:bg-gray-800 flex items-center justify-center">
                                                <svg class="w-4 h-4 text-gray-600 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                                </svg>
                                            </div>
                                    @endswitch
                                </div>
                                
                                <!-- Content -->
                                <div class="flex-grow min-w-0">
                                    <div class="flex items-center gap-2 flex-wrap">
                                        <span class="font-medium text-gray-900 dark:text-white">
                                            {{ $activity->user->name ?? 'System' }}
                                        </span>
                                        <span class="text-gray-500 dark:text-gray-400">•</span>
                                        <span class="text-xs px-2 py-0.5 rounded-full
                                            @if($activity->type === 'stage_change') bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400
                                            @elseif($activity->type === 'approval') bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400
                                            @elseif($activity->type === 'rejection') bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400
                                            @else bg-gray-100 text-gray-700 dark:bg-gray-800 dark:text-gray-400
                                            @endif">
                                            {{ $types[$activity->type] ?? ucfirst($activity->type) }}
                                        </span>
                                    </div>
                                    
                                    <p class="text-sm text-gray-700 dark:text-gray-300 mt-1">
                                        {{ Str::limit($activity->content, 200) }}
                                    </p>
                                    
                                    <div class="flex items-center gap-3 mt-2 text-xs text-gray-500 dark:text-gray-400">
                                        @if($activity->asset)
                                            <a href="{{ route('kanban.assets.show', $activity->asset_id) }}" 
                                               class="hover:text-brand-500 dark:hover:text-brand-400 flex items-center gap-1">
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                                </svg>
                                                {{ $activity->asset->asset_code ?? 'Asset' }}
                                            </a>
                                            @if($activity->asset->project)
                                                <span>•</span>
                                                <a href="{{ route('kanban.projects.show', $activity->asset->project_id) }}" 
                                                   class="hover:text-brand-500 dark:hover:text-brand-400">
                                                    {{ $activity->asset->project->project_code ?? 'Project' }}
                                                </a>
                                            @endif
                                        @endif
                                        <span>•</span>
                                        <span>Stage {{ $activity->stage }}: {{ $stages[$activity->stage] ?? 'Unknown' }}</span>
                                        <span>•</span>
                                        <span title="{{ $activity->created_at->format('d M Y H:i:s') }}">
                                            {{ $activity->created_at->diffForHumans() }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
                
                <!-- Pagination -->
                <div class="p-4 border-t border-gray-200 dark:border-gray-800">
                    {{ $activities->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection

@extends('layouts.app')

@php
use App\Models\ActivityKanban;
use App\Models\ProjectKanban;
@endphp

@section('content')
<div>
    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Log Aktivitas</h1>
            <p class="text-gray-600 dark:text-gray-400 mt-1">Riwayat semua aktivitas proyek</p>
        </div>
    </div>

    {{-- Filters --}}
    <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-4 mb-6">
        <form action="{{ route('appraisal.activities.index') }}" method="GET" class="flex flex-col lg:flex-row gap-4">
            <div class="flex-1">
                <input type="text" name="search" value="{{ request('search') }}" 
                       placeholder="Cari aktivitas atau deskripsi..." 
                       class="input input-bordered w-full">
            </div>
            <select name="activity_type" class="select select-bordered w-full lg:w-40">
                <option value="">Semua Tipe</option>
                @foreach(ActivityKanban::TYPES as $key => $label)
                <option value="{{ $key }}" {{ request('activity_type') === $key ? 'selected' : '' }}>{{ $label }}</option>
                @endforeach
            </select>
            <select name="project_id" class="select select-bordered w-full lg:w-48">
                <option value="">Semua Proyek</option>
                @foreach($projects ?? [] as $project)
                <option value="{{ $project->id }}" {{ request('project_id') == $project->id ? 'selected' : '' }}>{{ $project->project_code }} - {{ Str::limit($project->name, 20) }}</option>
                @endforeach
            </select>
            <select name="user_id" class="select select-bordered w-full lg:w-48">
                <option value="">Semua User</option>
                @foreach($users ?? [] as $user)
                <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>{{ $user->name }}</option>
                @endforeach
            </select>
            <button type="submit" class="btn btn-primary">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
            </button>
            @if(request()->hasAny(['search', 'activity_type', 'project_id', 'user_id']))
            <a href="{{ route('appraisal.activities.index') }}" class="btn btn-ghost">Reset</a>
            @endif
        </form>
    </div>

    {{-- Activity Timeline --}}
    <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700">
        @forelse($activities->groupBy(fn($a) => $a->created_at->format('Y-m-d')) as $date => $dayActivities)
        <div class="border-b border-gray-200 dark:border-gray-700 last:border-b-0">
            {{-- Date Header --}}
            <div class="px-4 py-3 bg-gray-50 dark:bg-gray-700/50 sticky top-0">
                <h3 class="text-sm font-medium text-gray-700 dark:text-gray-300">
                    @if($date === now()->format('Y-m-d'))
                        Hari Ini
                    @elseif($date === now()->subDay()->format('Y-m-d'))
                        Kemarin
                    @else
                        {{ \Carbon\Carbon::parse($date)->translatedFormat('l, d F Y') }}
                    @endif
                </h3>
            </div>

            {{-- Activities List --}}
            <div class="divide-y divide-gray-100 dark:divide-gray-700">
                @foreach($dayActivities as $activity)
                <div class="px-4 py-4 hover:bg-gray-50 dark:hover:bg-gray-700/30">
                    <div class="flex items-start gap-4">
                        {{-- Icon --}}
                        <div class="flex-shrink-0">
                            @php
                                $iconBg = match($activity->activity_type ?? 'default') {
                                    'stage_move' => 'bg-purple-100 dark:bg-purple-900/30 text-purple-600 dark:text-purple-400',
                                    'comment' => 'bg-blue-100 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400',
                                    'approval' => 'bg-green-100 dark:bg-green-900/30 text-green-600 dark:text-green-400',
                                    'rejection' => 'bg-red-100 dark:bg-red-900/30 text-red-600 dark:text-red-400',
                                    'obstacle' => 'bg-yellow-100 dark:bg-yellow-900/30 text-yellow-600 dark:text-yellow-400',
                                    'upload' => 'bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-400',
                                    default => 'bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-400',
                                };
                            @endphp
                            <div class="w-10 h-10 rounded-full {{ $iconBg }} flex items-center justify-center">
                                @switch($activity->activity_type ?? 'default')
                                    @case('stage_move')
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4" />
                                        </svg>
                                        @break
                                    @case('comment')
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                                        </svg>
                                        @break
                                    @case('approval')
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                        </svg>
                                        @break
                                    @case('rejection')
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                        </svg>
                                        @break
                                    @case('obstacle')
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                        </svg>
                                        @break
                                    @case('upload')
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                                        </svg>
                                        @break
                                    @default
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                @endswitch
                            </div>
                        </div>

                        {{-- Content --}}
                        <div class="flex-1 min-w-0">
                            <div class="flex items-start justify-between gap-4">
                                <div>
                                    <p class="text-sm text-gray-900 dark:text-white">
                                        <span class="font-medium">{{ $activity->user->name ?? 'System' }}</span>
                                        <span class="text-gray-600 dark:text-gray-400">{{ $activity->description }}</span>
                                    </p>
                                    
                                    {{-- Project Reference --}}
                                    @if($activity->project)
                                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                        <span class="badge badge-ghost badge-xs">{{ ActivityKanban::TYPES[$activity->activity_type] ?? $activity->activity_type }}</span>
                                        <a href="{{ route('appraisal.projects.show', $activity->project) }}" class="text-blue-600 dark:text-blue-400 hover:underline ml-1">
                                            {{ $activity->project->project_code }} - {{ Str::limit($activity->project->name, 25) }}
                                        </a>
                                    </p>
                                    @endif

                                    {{-- Stage Context --}}
                                    @if($activity->stage_context)
                                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                        @php
                                            $stageLabel = ProjectKanban::STAGES[$activity->stage_context] ?? null;
                                            $stageColors = [
                                                'lead' => 'badge-ghost',
                                                'proposal' => 'badge-info',
                                                'contract' => 'badge-primary',
                                                'inspection' => 'badge-secondary',
                                                'analysis' => 'badge-accent',
                                                'review' => 'badge-warning',
                                                'client_approval' => 'badge-warning',
                                                'final_report' => 'badge-info',
                                                'invoicing' => 'badge-success',
                                                'done' => 'badge-success',
                                            ];
                                        @endphp
                                        Stage: 
                                        <span class="badge {{ $stageColors[$activity->stage_context] ?? 'badge-ghost' }} badge-xs">{{ $stageLabel ?? $activity->stage_context }}</span>
                                    </p>
                                    @endif
                                </div>

                                <span class="text-xs text-gray-400 dark:text-gray-500 whitespace-nowrap">
                                    {{ $activity->created_at->format('H:i') }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @empty
        <div class="text-center py-12">
            <svg class="w-12 h-12 mx-auto text-gray-300 dark:text-gray-600 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <p class="text-gray-500 dark:text-gray-400">Belum ada aktivitas tercatat</p>
        </div>
        @endforelse

        {{-- Pagination --}}
        @if($activities->hasPages())
        <div class="px-4 py-3 border-t border-gray-200 dark:border-gray-700">
            {{ $activities->links() }}
        </div>
        @endif
    </div>
</div>
@endsection

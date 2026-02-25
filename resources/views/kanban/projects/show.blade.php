@extends('layouts.app')

@section('title', 'Detail Proyek - ' . $project->name)

@section('content')
<div class="space-y-6">
    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4">
        <div>
            <a href="{{ route('kanban.projects.index') }}" class="inline-flex items-center text-sm text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300 mb-2">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
                Kembali ke Daftar
            </a>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">{{ $project->name }}</h1>
            <div class="flex items-center gap-3 mt-2">
                <span class="text-sm text-gray-500 dark:text-gray-400">{{ $project->project_code }}</span>
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                    {{ $project->status === 'active' ? 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400' : '' }}
                    {{ $project->status === 'completed' ? 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400' : '' }}
                    {{ $project->status === 'on_hold' ? 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-400' : '' }}
                    {{ $project->status === 'cancelled' ? 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400' : '' }}
                ">
                    {{ ucfirst(str_replace('_', ' ', $project->status)) }}
                </span>
            </div>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('kanban.projects.edit', $project) }}" class="inline-flex items-center gap-2 px-4 py-2 bg-gray-100 hover:bg-gray-200 dark:bg-gray-800 dark:hover:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-lg transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                </svg>
                Edit
            </a>
            <a href="{{ route('kanban.assets.create', ['project_id' => $project->id]) }}" class="inline-flex items-center gap-2 px-4 py-2 bg-brand-500 hover:bg-brand-600 text-white rounded-lg transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Tambah Asset
            </a>
        </div>
    </div>

    {{-- Alert Messages --}}
    @if(session('success'))
    <div class="p-4 rounded-lg bg-green-50 border border-green-200 dark:bg-green-900/20 dark:border-green-800">
        <p class="text-green-700 dark:text-green-400">{{ session('success') }}</p>
    </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Project Info --}}
        <div class="lg:col-span-1 space-y-6">
            {{-- Details --}}
            <div class="bg-white dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-800 p-6">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Detail Proyek</h2>
                <dl class="space-y-4">
                    <div>
                        <dt class="text-xs text-gray-500 dark:text-gray-400 uppercase">Klien</dt>
                        <dd class="mt-1">
                            @if($project->client)
                            <a href="{{ route('kanban.clients.show', $project->client) }}" class="text-gray-900 dark:text-white hover:text-brand-600 dark:hover:text-brand-400">
                                {{ $project->client->name }}
                            </a>
                            @else
                            <span class="text-gray-400">-</span>
                            @endif
                        </dd>
                    </div>
                    @if($project->description)
                    <div>
                        <dt class="text-xs text-gray-500 dark:text-gray-400 uppercase">Deskripsi</dt>
                        <dd class="mt-1 text-gray-900 dark:text-white">{{ $project->description }}</dd>
                    </div>
                    @endif
                    <div>
                        <dt class="text-xs text-gray-500 dark:text-gray-400 uppercase">Target Selesai</dt>
                        <dd class="mt-1 text-gray-900 dark:text-white">
                            {{ $project->due_date ? $project->due_date->format('d F Y') : '-' }}
                        </dd>
                    </div>
                    <div>
                        <dt class="text-xs text-gray-500 dark:text-gray-400 uppercase">Dibuat</dt>
                        <dd class="mt-1 text-gray-900 dark:text-white">{{ $project->created_at->format('d F Y') }}</dd>
                    </div>
                </dl>
            </div>

            {{-- Stats --}}
            <div class="bg-white dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-800 p-6">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Statistik</h2>
                <div class="grid grid-cols-2 gap-4">
                    <div class="text-center p-3 rounded-lg bg-gray-50 dark:bg-gray-800">
                        <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $project->assets->count() }}</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Total Asset</p>
                    </div>
                    <div class="text-center p-3 rounded-lg bg-green-50 dark:bg-green-900/20">
                        <p class="text-2xl font-bold text-green-600 dark:text-green-400">{{ $project->assets->where('current_stage', 13)->count() }}</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Selesai</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Assets List --}}
        <div class="lg:col-span-2">
            <div class="bg-white dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-800">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-800">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Objek Penilaian ({{ $project->assets->count() }})</h2>
                </div>
                <div class="divide-y divide-gray-200 dark:divide-gray-800">
                    @forelse($project->assets as $asset)
                    <a href="{{ route('kanban.assets.show', $asset) }}" class="block p-4 hover:bg-gray-50 dark:hover:bg-gray-800/50 transition">
                        <div class="flex items-center justify-between">
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center gap-2">
                                    <p class="font-medium text-gray-900 dark:text-white truncate">{{ $asset->name }}</p>
                                    @if($asset->priority === 'critical')
                                    <span class="flex-shrink-0 w-2 h-2 rounded-full bg-red-500"></span>
                                    @elseif($asset->priority === 'high')
                                    <span class="flex-shrink-0 w-2 h-2 rounded-full bg-orange-500"></span>
                                    @endif
                                </div>
                                <p class="text-sm text-gray-500 dark:text-gray-400">{{ $asset->asset_code }} • {{ $asset->asset_type_name }}</p>
                            </div>
                            <div class="text-right ml-4">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-brand-100 text-brand-800 dark:bg-brand-900/30 dark:text-brand-400">
                                    {{ $asset->stage_name }}
                                </span>
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Stage {{ $asset->current_stage }}/13</p>
                            </div>
                        </div>
                    </a>
                    @empty
                    <div class="p-8 text-center">
                        <svg class="w-12 h-12 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                        </svg>
                        <p class="text-gray-500 dark:text-gray-400">Belum ada objek penilaian</p>
                        <a href="{{ route('kanban.assets.create', ['project_id' => $project->id]) }}" class="mt-2 inline-block text-brand-600 hover:text-brand-700 dark:text-brand-400 font-medium">
                            Tambah asset pertama →
                        </a>
                    </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

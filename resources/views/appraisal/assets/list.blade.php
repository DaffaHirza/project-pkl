@extends('layouts.app')

@section('title', 'Daftar Objek Penilaian')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Daftar Objek Penilaian</h1>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Semua objek penilaian dalam bentuk list</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('appraisal.assets.index') }}" 
               class="inline-flex items-center gap-2 rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-300 dark:hover:bg-gray-700">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17V7m0 10a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2h2a2 2 0 012 2m0 10a2 2 0 002 2h2a2 2 0 002-2M9 7a2 2 0 012-2h2a2 2 0 012 2m0 10V7m0 10a2 2 0 002 2h2a2 2 0 002-2V7a2 2 0 00-2-2h-2a2 2 0 00-2 2" />
                </svg>
                Kanban View
            </a>
            <a href="{{ route('appraisal.assets.create') }}" 
               class="inline-flex items-center gap-2 rounded-lg bg-brand-500 px-4 py-2.5 text-sm font-medium text-white hover:bg-brand-600 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Tambah Objek
            </a>
        </div>
    </div>

    <!-- Filters -->
    <div class="rounded-xl border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-gray-900">
        <form method="GET" action="{{ route('appraisal.assets.list') }}" class="flex flex-wrap items-end gap-4">
            <!-- Project Filter -->
            <div class="flex-1 min-w-[200px]">
                <label for="project_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Proyek</label>
                <select id="project_id" name="project_id" 
                        class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white text-sm">
                    <option value="">Semua Proyek</option>
                    @foreach($projects as $project)
                        <option value="{{ $project->id }}" {{ request('project_id') == $project->id ? 'selected' : '' }}>
                            {{ $project->project_code }} - {{ $project->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Stage Filter -->
            <div class="flex-1 min-w-[150px]">
                <label for="stage" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Stage</label>
                <select id="stage" name="stage" 
                        class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white text-sm">
                    <option value="">Semua Stage</option>
                    @foreach($stages as $key => $label)
                        <option value="{{ $key }}" {{ request('stage') == $key ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Asset Type Filter -->
            <div class="flex-1 min-w-[150px]">
                <label for="asset_type" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Jenis Aset</label>
                <select id="asset_type" name="asset_type" 
                        class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white text-sm">
                    <option value="">Semua Jenis</option>
                    @foreach($assetTypes as $key => $label)
                        <option value="{{ $key }}" {{ request('asset_type') == $key ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Priority Filter -->
            <div class="flex-1 min-w-[120px]">
                <label for="priority" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Prioritas</label>
                <select id="priority" name="priority" 
                        class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white text-sm">
                    <option value="">Semua</option>
                    <option value="normal" {{ request('priority') == 'normal' ? 'selected' : '' }}>Normal</option>
                    <option value="warning" {{ request('priority') == 'warning' ? 'selected' : '' }}>Warning</option>
                    <option value="critical" {{ request('priority') == 'critical' ? 'selected' : '' }}>Critical</option>
                </select>
            </div>

            <!-- Search -->
            <div class="flex-1 min-w-[200px]">
                <label for="search" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Cari</label>
                <input type="text" id="search" name="search" value="{{ request('search') }}" 
                       placeholder="Nama atau kode objek..."
                       class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white text-sm">
            </div>

            <div class="flex gap-2">
                <button type="submit" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 rounded-lg text-sm font-medium">
                    Filter
                </button>
                <a href="{{ route('appraisal.assets.list') }}" class="px-4 py-2 text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white text-sm">
                    Reset
                </a>
            </div>
        </form>
    </div>

    <!-- Table -->
    <div class="rounded-xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700">
                    <tr>
                        <th class="px-4 py-3 text-left font-semibold text-gray-900 dark:text-white">Kode</th>
                        <th class="px-4 py-3 text-left font-semibold text-gray-900 dark:text-white">Nama Objek</th>
                        <th class="px-4 py-3 text-left font-semibold text-gray-900 dark:text-white">Proyek</th>
                        <th class="px-4 py-3 text-left font-semibold text-gray-900 dark:text-white">Klien</th>
                        <th class="px-4 py-3 text-left font-semibold text-gray-900 dark:text-white">Jenis</th>
                        <th class="px-4 py-3 text-left font-semibold text-gray-900 dark:text-white">Stage</th>
                        <th class="px-4 py-3 text-left font-semibold text-gray-900 dark:text-white">Prioritas</th>
                        <th class="px-4 py-3 text-left font-semibold text-gray-900 dark:text-white">Target</th>
                        <th class="px-4 py-3 text-center font-semibold text-gray-900 dark:text-white">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse($assets as $asset)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/50">
                        <td class="px-4 py-3">
                            <span class="font-mono text-xs text-gray-600 dark:text-gray-400">{{ $asset->asset_code }}</span>
                        </td>
                        <td class="px-4 py-3">
                            <a href="{{ route('appraisal.assets.show', $asset) }}" 
                               class="font-medium text-gray-900 dark:text-white hover:text-brand-500 dark:hover:text-brand-400">
                                {{ Str::limit($asset->name, 40) }}
                            </a>
                            @if($asset->location_address)
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">{{ Str::limit($asset->location_address, 50) }}</p>
                            @endif
                        </td>
                        <td class="px-4 py-3">
                            <a href="{{ route('appraisal.projects.show', $asset->project) }}" 
                               class="text-gray-700 dark:text-gray-300 hover:text-brand-500 dark:hover:text-brand-400">
                                {{ $asset->project->project_code ?? '-' }}
                            </a>
                        </td>
                        <td class="px-4 py-3 text-gray-600 dark:text-gray-400">
                            {{ $asset->project->client->name ?? '-' }}
                        </td>
                        <td class="px-4 py-3">
                            <span class="inline-flex items-center rounded-full px-2 py-0.5 text-xs font-medium
                                @if($asset->asset_type === 'tanah') bg-amber-100 text-amber-800 dark:bg-amber-900/30 dark:text-amber-400
                                @elseif($asset->asset_type === 'bangunan') bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400
                                @elseif($asset->asset_type === 'tanah_bangunan') bg-indigo-100 text-indigo-800 dark:bg-indigo-900/30 dark:text-indigo-400
                                @else bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300
                                @endif">
                                {{ $assetTypes[$asset->asset_type] ?? $asset->asset_type }}
                            </span>
                        </td>
                        <td class="px-4 py-3">
                            <span class="inline-flex items-center rounded-full px-2 py-0.5 text-xs font-medium
                                @if($asset->current_stage === 'done') bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400
                                @elseif(in_array($asset->current_stage, ['inspection', 'analysis'])) bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400
                                @elseif(in_array($asset->current_stage, ['review', 'client_approval'])) bg-purple-100 text-purple-800 dark:bg-purple-900/30 dark:text-purple-400
                                @else bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300
                                @endif">
                                {{ $stages[$asset->current_stage] ?? $asset->current_stage }}
                            </span>
                        </td>
                        <td class="px-4 py-3">
                            <span class="inline-flex items-center gap-1 text-xs font-medium
                                @if($asset->priority_status === 'critical') text-red-600 dark:text-red-400
                                @elseif($asset->priority_status === 'warning') text-yellow-600 dark:text-yellow-400
                                @else text-green-600 dark:text-green-400
                                @endif">
                                <span class="w-2 h-2 rounded-full 
                                    @if($asset->priority_status === 'critical') bg-red-500
                                    @elseif($asset->priority_status === 'warning') bg-yellow-500
                                    @else bg-green-500
                                    @endif"></span>
                                {{ ucfirst($asset->priority_status) }}
                            </span>
                        </td>
                        <td class="px-4 py-3">
                            @if($asset->target_completion_date)
                                <span class="text-xs {{ $asset->target_completion_date->isPast() ? 'text-red-600 dark:text-red-400 font-medium' : 'text-gray-600 dark:text-gray-400' }}">
                                    {{ $asset->target_completion_date->format('d M Y') }}
                                </span>
                            @else
                                <span class="text-xs text-gray-400">-</span>
                            @endif
                        </td>
                        <td class="px-4 py-3">
                            <div class="flex items-center justify-center gap-1">
                                <a href="{{ route('appraisal.assets.show', $asset) }}" 
                                   class="p-1.5 text-gray-500 hover:text-brand-500 dark:text-gray-400 dark:hover:text-brand-400"
                                   title="Lihat Detail">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    </svg>
                                </a>
                                <a href="{{ route('appraisal.assets.edit', $asset) }}" 
                                   class="p-1.5 text-gray-500 hover:text-blue-500 dark:text-gray-400 dark:hover:text-blue-400"
                                   title="Edit">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                    </svg>
                                </a>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="px-4 py-12 text-center">
                            <svg class="mx-auto h-12 w-12 text-gray-300 dark:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                            </svg>
                            <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">Tidak ada objek penilaian</h3>
                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Mulai dengan menambahkan objek penilaian baru.</p>
                            <a href="{{ route('appraisal.assets.create') }}" 
                               class="inline-flex items-center gap-2 mt-4 px-4 py-2 bg-brand-500 hover:bg-brand-600 text-white text-sm font-medium rounded-lg">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                </svg>
                                Tambah Objek
                            </a>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($assets->hasPages())
        <div class="border-t border-gray-200 dark:border-gray-700 px-4 py-3">
            {{ $assets->links() }}
        </div>
        @endif
    </div>
</div>
@endsection

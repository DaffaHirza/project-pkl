@extends('layouts.app')

@section('title', 'Objek Penilaian')

@section('content')
<div>
    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Objek Penilaian</h1>
            <p class="text-gray-600 dark:text-gray-400 mt-1">Kelola asset yang dinilai</p>
        </div>
        <a href="{{ route('kanban.assets.create') }}" class="inline-flex items-center gap-2 px-4 py-2.5 bg-brand-500 hover:bg-brand-600 text-white rounded-lg font-medium text-sm transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            Tambah Asset
        </a>
    </div>

    {{-- Alert Messages --}}
    @if(session('success'))
    <div class="mb-6 p-4 rounded-lg bg-green-50 border border-green-200 dark:bg-green-900/20 dark:border-green-800">
        <p class="text-green-700 dark:text-green-400">{{ session('success') }}</p>
    </div>
    @endif

    {{-- Filters --}}
    <div class="bg-white dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-800 p-4 mb-6">
        <form action="{{ route('kanban.assets.index') }}" method="GET" class="flex flex-col lg:flex-row gap-4">
            <div class="flex-1">
                <input type="text" name="search" value="{{ request('search') }}" 
                       placeholder="Cari nama, kode asset..." 
                       class="w-full px-4 py-2 rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-brand-500">
            </div>
            <select name="stage" class="px-4 py-2 rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-900 dark:text-white">
                <option value="">Semua Stage</option>
                @foreach(\App\Models\ProjectAssetKanban::STAGES as $num => $name)
                <option value="{{ $num }}" {{ request('stage') == $num ? 'selected' : '' }}>{{ $num }}. {{ $name }}</option>
                @endforeach
            </select>
            <select name="priority" class="px-4 py-2 rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-900 dark:text-white">
                <option value="">Semua Prioritas</option>
                <option value="critical" {{ request('priority') === 'critical' ? 'selected' : '' }}>Kritikal</option>
                <option value="high" {{ request('priority') === 'high' ? 'selected' : '' }}>Tinggi</option>
                <option value="normal" {{ request('priority') === 'normal' ? 'selected' : '' }}>Normal</option>
                <option value="low" {{ request('priority') === 'low' ? 'selected' : '' }}>Rendah</option>
            </select>
            <button type="submit" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 dark:bg-gray-800 dark:hover:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-lg transition">
                Filter
            </button>
            @if(request()->hasAny(['search', 'stage', 'priority']))
            <a href="{{ route('kanban.assets.index') }}" class="px-4 py-2 text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white transition">Reset</a>
            @endif
        </form>
    </div>

    {{-- Stage Overview --}}
    <div class="mb-6 overflow-x-auto pb-2">
        <div class="flex gap-2 min-w-max">
            @php $stages = \App\Models\ProjectAssetKanban::STAGES; @endphp
            @foreach($stages as $num => $name)
            @php $count = $assets->where('current_stage', $num)->count(); @endphp
            <a href="{{ route('kanban.assets.index', ['stage' => $num]) }}" 
               class="flex items-center gap-2 px-3 py-2 rounded-lg border transition
                   {{ request('stage') == $num ? 'bg-brand-50 border-brand-300 dark:bg-brand-900/20 dark:border-brand-700' : 'bg-white border-gray-200 hover:border-gray-300 dark:bg-gray-900 dark:border-gray-800 dark:hover:border-gray-700' }}">
                <span class="text-xs font-medium {{ request('stage') == $num ? 'text-brand-700 dark:text-brand-400' : 'text-gray-600 dark:text-gray-400' }}">{{ $num }}</span>
                <span class="text-xs {{ request('stage') == $num ? 'text-brand-600 dark:text-brand-300' : 'text-gray-500 dark:text-gray-500' }}">{{ Str::limit($name, 10) }}</span>
                <span class="text-xs font-bold px-1.5 py-0.5 rounded {{ $count > 0 ? 'bg-brand-500 text-white' : 'bg-gray-200 text-gray-600 dark:bg-gray-700 dark:text-gray-400' }}">{{ $count }}</span>
            </a>
            @endforeach
        </div>
    </div>

    {{-- Asset Grid --}}
    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
        @forelse($assets as $asset)
        <a href="{{ route('kanban.assets.show', $asset) }}" class="block bg-white dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-800 hover:border-brand-300 dark:hover:border-brand-700 hover:shadow-md transition p-5">
            <div class="flex items-start justify-between mb-3">
                <div class="flex-1 min-w-0">
                    <p class="font-medium text-gray-900 dark:text-white truncate">{{ $asset->name }}</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400">{{ $asset->asset_code }}</p>
                </div>
                @if($asset->priority === 'critical')
                <span class="flex-shrink-0 px-2 py-0.5 text-xs font-medium bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400 rounded">Kritikal</span>
                @elseif($asset->priority === 'high')
                <span class="flex-shrink-0 px-2 py-0.5 text-xs font-medium bg-orange-100 text-orange-700 dark:bg-orange-900/30 dark:text-orange-400 rounded">Tinggi</span>
                @endif
            </div>
            
            {{-- Stage Progress --}}
            <div class="mb-3">
                <div class="flex items-center justify-between text-xs mb-1">
                    <span class="text-gray-500 dark:text-gray-400">Progress</span>
                    <span class="font-medium text-gray-900 dark:text-white">{{ $asset->current_stage }}/13</span>
                </div>
                <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-1.5">
                    <div class="bg-brand-500 h-1.5 rounded-full" style="width: {{ ($asset->current_stage / 13) * 100 }}%"></div>
                </div>
            </div>

            <div class="flex items-center justify-between text-xs">
                <span class="text-gray-500 dark:text-gray-400">{{ $asset->project->name ?? '-' }}</span>
                <span class="inline-flex items-center px-2 py-0.5 rounded bg-gray-100 dark:bg-gray-800 text-gray-600 dark:text-gray-400">
                    {{ $asset->stage_label }}
                </span>
            </div>
        </a>
        @empty
        <div class="col-span-full">
            <div class="bg-white dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-800 p-12 text-center">
                <svg class="w-12 h-12 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                </svg>
                <p class="text-gray-500 dark:text-gray-400">Belum ada objek penilaian</p>
                <a href="{{ route('kanban.assets.create') }}" class="mt-2 inline-block text-brand-600 hover:text-brand-700 dark:text-brand-400 font-medium">Tambah asset pertama →</a>
            </div>
        </div>
        @endforelse
    </div>

    {{-- Pagination --}}
    @if($assets->hasPages())
    <div class="mt-6">
        {{ $assets->withQueryString()->links() }}
    </div>
    @endif
</div>
@endsection

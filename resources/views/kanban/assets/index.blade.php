@extends('layouts.app')

@section('title', 'Objek Penilaian')

@section('content')
<div>
    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div>
            <h1 class="text-xl font-semibold text-gray-900 dark:text-white">Objek Penilaian</h1>
            <p class="text-sm text-gray-600 dark:text-gray-400">Kelola asset yang dinilai</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('kanban.assets.board') }}" class="inline-flex items-center gap-2 px-3 py-2 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-lg text-sm transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17V7m0 10a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2h2a2 2 0 012 2m0 10a2 2 0 002 2h2a2 2 0 002-2M9 7a2 2 0 012-2h2a2 2 0 012 2m0 10V7m0 10a2 2 0 002 2h2a2 2 0 002-2V7a2 2 0 00-2-2h-2a2 2 0 00-2 2" />
                </svg>
                Kanban
            </a>
            <a href="{{ route('kanban.assets.create') }}" class="inline-flex items-center gap-2 px-3 py-2 bg-gray-800 hover:bg-gray-700 dark:bg-gray-100 dark:hover:bg-gray-200 text-white dark:text-gray-900 rounded-lg text-sm transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Tambah Asset
            </a>
        </div>
    </div>

    {{-- Alert Messages --}}
    @if(session('success'))
    <div class="mb-6 p-4 rounded-lg bg-green-50 border border-green-200 dark:bg-green-900/20 dark:border-green-800 flex items-center gap-3">
        <svg class="w-5 h-5 text-green-600 dark:text-green-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
        </svg>
        <p class="text-green-700 dark:text-green-400">{{ session('success') }}</p>
    </div>
    @endif

    {{-- Filters --}}
    <div class="bg-white dark:bg-gray-900 rounded-lg border border-gray-200 dark:border-gray-700 p-4 mb-6">
        <form action="{{ route('kanban.assets.index') }}" method="GET" class="flex flex-col lg:flex-row gap-3">
            <div class="flex-1 relative">
                <svg class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
                <input type="text" name="search" value="{{ request('search') }}" 
                       placeholder="Cari nama, kode asset..." 
                       class="w-full pl-9 pr-4 py-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-gray-400 focus:border-gray-400 transition text-sm">
            </div>
            <div class="relative">
                <select name="stage" class="appearance-none pl-3 pr-8 py-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-gray-400 text-sm">
                    <option value="">Semua Stage</option>
                    @foreach(\App\Models\AssetKanban::STAGES as $num => $name)
                    <option value="{{ $num }}" {{ request('stage') == $num ? 'selected' : '' }}>{{ $num }}. {{ $name }}</option>
                    @endforeach
                </select>
                <svg class="w-4 h-4 absolute right-2 top-1/2 -translate-y-1/2 text-gray-400 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                </svg>
            </div>
            <div class="relative">
                <select name="priority" class="appearance-none pl-3 pr-8 py-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-gray-400 text-sm">
                    <option value="">Semua Prioritas</option>
                    <option value="critical" {{ request('priority') === 'critical' ? 'selected' : '' }}>Kritikal</option>
                    <option value="high" {{ request('priority') === 'high' ? 'selected' : '' }}>Tinggi</option>
                    <option value="normal" {{ request('priority') === 'normal' ? 'selected' : '' }}>Normal</option>
                    <option value="low" {{ request('priority') === 'low' ? 'selected' : '' }}>Rendah</option>
                </select>
                <svg class="w-4 h-4 absolute right-2 top-1/2 -translate-y-1/2 text-gray-400 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                </svg>
            </div>
            <button type="submit" class="px-4 py-2 bg-gray-800 dark:bg-gray-100 hover:bg-gray-700 dark:hover:bg-gray-200 text-white dark:text-gray-900 rounded-lg transition text-sm">
                Filter
            </button>
            @if(request()->hasAny(['search', 'stage', 'priority']))
            <a href="{{ route('kanban.assets.index') }}" class="px-4 py-2 text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white transition text-center text-sm">Reset</a>
            @endif
        </form>
    </div>

    {{-- Stage Overview --}}
    <div class="mb-6 overflow-x-auto pb-2">
        <div class="flex gap-2 min-w-max">
            @php $stages = \App\Models\AssetKanban::STAGES; @endphp
            @foreach($stages as $num => $name)
            @php $count = $stageCounts[$num] ?? 0; @endphp
            <a href="{{ route('kanban.assets.index', ['stage' => $num]) }}" 
               class="flex items-center gap-2 px-3 py-2 rounded-lg border transition
                   {{ request('stage') == $num 
                        ? 'bg-gray-800 text-white border-gray-800 dark:bg-gray-100 dark:text-gray-900 dark:border-gray-100' 
                        : 'bg-white border-gray-200 hover:border-gray-300 dark:bg-gray-800 dark:border-gray-700 dark:hover:border-gray-600' }}">
                <span class="w-5 h-5 rounded {{ request('stage') == $num ? 'bg-white/20 dark:bg-gray-900/20' : 'bg-gray-100 dark:bg-gray-700' }} text-xs font-medium flex items-center justify-center {{ request('stage') == $num ? '' : 'text-gray-600 dark:text-gray-300' }}">{{ $num }}</span>
                <span class="text-sm {{ request('stage') == $num ? '' : 'text-gray-700 dark:text-gray-300' }}">{{ Str::limit($name, 10) }}</span>
                <span class="text-xs px-1.5 py-0.5 rounded {{ request('stage') == $num ? 'bg-white/20 dark:bg-gray-900/20' : ($count > 0 ? 'bg-gray-800 text-white dark:bg-gray-100 dark:text-gray-900' : 'bg-gray-100 text-gray-500 dark:bg-gray-700 dark:text-gray-400') }}">{{ $count }}</span>
            </a>
            @endforeach
        </div>
    </div>

    {{-- Asset Grid --}}
    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
        @forelse($assets as $asset)
        <a href="{{ route('kanban.assets.show', $asset) }}" class="group block bg-white dark:bg-gray-900 rounded-lg border border-gray-200 dark:border-gray-700 hover:border-gray-300 dark:hover:border-gray-600 hover:shadow-sm transition p-4">
            <div class="flex items-start justify-between mb-3">
                <div class="flex-1 min-w-0">
                    <p class="font-medium text-gray-900 dark:text-white truncate">{{ $asset->name }}</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5 flex items-center gap-1">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                        </svg>
                        {{ ucfirst(str_replace('_', ' ', $asset->asset_type)) }}
                    </p>
                </div>
                @if($asset->priority === 'critical')
                <span class="flex-shrink-0 px-2 py-0.5 text-xs bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400 rounded">
                    Kritikal
                </span>
                @elseif($asset->priority === 'high')
                <span class="flex-shrink-0 px-2 py-0.5 text-xs bg-orange-100 text-orange-700 dark:bg-orange-900/30 dark:text-orange-400 rounded">Tinggi</span>
                @endif
            </div>
            
            {{-- Stage Progress --}}
            <div class="mb-3">
                <div class="flex items-center justify-between text-xs mb-1.5">
                    <span class="text-gray-500 dark:text-gray-400">Progress</span>
                    <span class="text-gray-700 dark:text-gray-300">{{ $asset->current_stage }}/13</span>
                </div>
                <div class="w-full bg-gray-100 dark:bg-gray-800 rounded-full h-1.5 overflow-hidden">
                    <div class="bg-gray-600 dark:bg-gray-400 h-1.5 rounded-full transition-all" style="width: {{ ($asset->current_stage / 13) * 100 }}%"></div>
                </div>
            </div>

            <div class="flex items-center justify-between">
                <span class="text-xs text-gray-500 dark:text-gray-400 flex items-center gap-1">
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                    {{ $asset->client->name ?? '-' }}
                </span>
                <span class="text-xs text-gray-600 dark:text-gray-400 bg-gray-100 dark:bg-gray-800 px-2 py-0.5 rounded">
                    {{ $asset->stage_label }}
                </span>
            </div>
        </a>
        @empty
        <div class="col-span-full">
            <div class="bg-white dark:bg-gray-900 rounded-lg border border-gray-200 dark:border-gray-700 p-12 text-center">
                <div class="w-12 h-12 rounded-lg bg-gray-100 dark:bg-gray-800 flex items-center justify-center mx-auto mb-4">
                    <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                    </svg>
                </div>
                <h3 class="font-medium text-gray-900 dark:text-white mb-1">Belum Ada Objek Penilaian</h3>
                <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">Mulai tambahkan asset yang akan dinilai</p>
                <a href="{{ route('kanban.assets.create') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-gray-800 hover:bg-gray-700 dark:bg-gray-100 dark:hover:bg-gray-200 text-white dark:text-gray-900 rounded-lg text-sm transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Tambah Asset Pertama
                </a>
            </div>
        </div>
        @endforelse
    </div>

    {{-- Pagination --}}
    @if($assets->hasPages())
    <div class="mt-8">
        {{ $assets->withQueryString()->links() }}
    </div>
    @endif
</div>
@endsection

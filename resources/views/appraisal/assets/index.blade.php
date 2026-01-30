@extends('layouts.app')

@section('title', 'Kanban Objek Penilaian')

@section('content')
<div x-data="kanbanBoard()" class="h-[calc(100vh-140px)] flex flex-col">
    <!-- Header -->
    <div class="flex-shrink-0 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Kanban Objek Penilaian</h1>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Drag & drop objek untuk memindahkan tahap workflow</p>
        </div>
        <div class="flex flex-wrap items-center gap-3">
            <!-- Filter by Project -->
            <select x-model="filterProject" @change="applyFilters()" 
                    class="rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm dark:border-gray-700 dark:bg-gray-800 dark:text-white">
                <option value="">Semua Proyek</option>
                @foreach($projects as $project)
                <option value="{{ $project->id }}">{{ $project->project_code }}</option>
                @endforeach
            </select>

            <!-- Filter by Asset Type -->
            <select x-model="filterType" @change="applyFilters()" 
                    class="rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm dark:border-gray-700 dark:bg-gray-800 dark:text-white">
                <option value="">Semua Jenis</option>
                @foreach($assetTypes as $key => $label)
                <option value="{{ $key }}">{{ $label }}</option>
                @endforeach
            </select>

            <!-- Filter by Priority -->
            <select x-model="filterPriority" @change="applyFilters()" 
                    class="rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm dark:border-gray-700 dark:bg-gray-800 dark:text-white">
                <option value="">Semua Prioritas</option>
                <option value="normal">Normal</option>
                <option value="warning">Perlu Perhatian</option>
                <option value="critical">Kritis</option>
            </select>

            <!-- Search -->
            <div class="relative">
                <input type="text" x-model="searchQuery" @input.debounce.300ms="applyFilters()"
                       placeholder="Cari objek..."
                       class="w-56 rounded-lg border border-gray-300 bg-white pl-10 pr-4 py-2 text-sm dark:border-gray-700 dark:bg-gray-800 dark:text-white">
                <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
            </div>

            <!-- Toggle View -->
            <div class="flex items-center rounded-lg border border-gray-300 dark:border-gray-700">
                <a href="{{ route('appraisal.assets.index') }}" 
                   class="px-3 py-2 text-sm font-medium rounded-l-lg bg-brand-500 text-white">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17V7m0 10a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2h2a2 2 0 012 2m0 10a2 2 0 002 2h2a2 2 0 002-2M9 7a2 2 0 012-2h2a2 2 0 012 2m0 10V7m0 10a2 2 0 002 2h2a2 2 0 002-2V7a2 2 0 00-2-2h-2a2 2 0 00-2 2" />
                    </svg>
                </a>
                <a href="{{ route('appraisal.assets.list') }}" 
                   class="px-3 py-2 text-sm font-medium rounded-r-lg text-gray-600 hover:bg-gray-100 dark:text-gray-400 dark:hover:bg-gray-800">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16" />
                    </svg>
                </a>
            </div>

            <!-- New Asset Button -->
            <a href="{{ route('appraisal.assets.create') }}" 
               class="inline-flex items-center gap-2 rounded-lg bg-brand-500 px-4 py-2 text-sm font-medium text-white hover:bg-brand-600 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Objek Baru
            </a>
        </div>
    </div>

    <!-- Kanban Board -->
    <div class="flex-1 overflow-x-auto">
        <div class="flex gap-4 h-full pb-4" style="min-width: max-content;">
            @foreach($stages as $stageKey => $stageLabel)
            <div class="flex-shrink-0 w-80 flex flex-col bg-gray-100 dark:bg-gray-800/50 rounded-xl">
                <!-- Column Header -->
                <div class="flex-shrink-0 px-4 py-3 border-b border-gray-200 dark:border-gray-700">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <h3 class="font-semibold text-gray-900 dark:text-white text-sm">{{ $stageLabel }}</h3>
                            <span class="inline-flex items-center justify-center w-6 h-6 text-xs font-medium rounded-full 
                                @if($stageKey === 'done') bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400
                                @elseif($stageKey === 'pending') bg-gray-200 text-gray-700 dark:bg-gray-700 dark:text-gray-300
                                @elseif(in_array($stageKey, ['inspection', 'analysis'])) bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400
                                @elseif(in_array($stageKey, ['review', 'client_approval'])) bg-purple-100 text-purple-700 dark:bg-purple-900/30 dark:text-purple-400
                                @else bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-400
                                @endif">
                                {{ count($assetsByStage[$stageKey] ?? []) }}
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Column Content -->
                <div class="flex-1 overflow-y-auto p-3 space-y-3"
                     x-data
                     x-init="Sortable.create($el, {
                         group: 'assets',
                         animation: 150,
                         ghostClass: 'opacity-50',
                         dragClass: 'shadow-2xl',
                         onEnd: function(evt) {
                             if (evt.from !== evt.to) {
                                 moveAsset(evt.item.dataset.assetId, '{{ $stageKey }}');
                             }
                         }
                     })">
                    @forelse($assetsByStage[$stageKey] ?? [] as $asset)
                    @include('appraisal.components.asset-card', ['asset' => $asset, 'assetTypes' => $assetTypes])
                    @empty
                    <div class="text-center py-8 text-gray-400 dark:text-gray-500 text-sm">
                        <svg class="mx-auto h-8 w-8 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                        </svg>
                        Tidak ada objek
                    </div>
                    @endforelse
                </div>
            </div>
            @endforeach
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
<script>
function kanbanBoard() {
    return {
        filterProject: '{{ request('project_id') }}',
        filterType: '{{ request('asset_type') }}',
        filterPriority: '{{ request('priority') }}',
        searchQuery: '{{ request('search') }}',
        
        applyFilters() {
            const params = new URLSearchParams();
            if (this.filterProject) params.append('project_id', this.filterProject);
            if (this.filterType) params.append('asset_type', this.filterType);
            if (this.filterPriority) params.append('priority', this.filterPriority);
            if (this.searchQuery) params.append('search', this.searchQuery);
            
            window.location.href = `{{ route('appraisal.assets.index') }}?${params.toString()}`;
        },
        
        moveAsset(assetId, newStage) {
            fetch(`/appraisal/assets/${assetId}/move-stage`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ stage: newStage })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showNotification('success', data.message || 'Objek berhasil dipindahkan');
                } else {
                    showNotification('error', data.message || 'Gagal memindahkan objek');
                    window.location.reload();
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showNotification('error', 'Terjadi kesalahan');
                window.location.reload();
            });
        }
    }
}

function showNotification(type, message) {
    const notification = document.createElement('div');
    notification.className = `fixed bottom-4 right-4 px-4 py-3 rounded-lg shadow-lg z-50 transform transition-all duration-300 ${
        type === 'success' ? 'bg-green-500 text-white' : 'bg-red-500 text-white'
    }`;
    notification.innerHTML = `
        <div class="flex items-center gap-2">
            ${type === 'success' 
                ? '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>'
                : '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>'
            }
            <span>${message}</span>
        </div>
    `;
    document.body.appendChild(notification);

    setTimeout(() => {
        notification.classList.add('opacity-0', 'translate-y-2');
        setTimeout(() => notification.remove(), 300);
    }, 3000);
}
</script>
@endpush
@endsection

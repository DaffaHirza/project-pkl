@extends('layouts.app')

@section('title', 'Kanban Proyek Penilaian')

@section('content')
<div x-data="kanbanBoard()" class="h-[calc(100vh-140px)] flex flex-col">
    <!-- Header -->
    <div class="flex-shrink-0 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Kanban Proyek</h1>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Kelola alur pekerjaan penilaian properti</p>
        </div>
        <div class="flex items-center gap-3">
            <!-- Filter by Priority -->
            <select x-model="filterPriority" @change="applyFilters()" 
                    class="rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm dark:border-gray-700 dark:bg-gray-800 dark:text-white">
                <option value="">Semua Prioritas</option>
                <option value="normal">Normal</option>
                <option value="warning">Perlu Perhatian</option>
                <option value="critical">Kritis</option>
            </select>

            <!-- Filter by Client -->
            <select x-model="filterClient" @change="applyFilters()"
                    class="rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm dark:border-gray-700 dark:bg-gray-800 dark:text-white">
                <option value="">Semua Klien</option>
                @foreach($clients as $client)
                <option value="{{ $client->id }}">{{ $client->name }}</option>
                @endforeach
            </select>

            <!-- Search -->
            <div class="relative">
                <input type="text" x-model="searchQuery" @input.debounce.300ms="applyFilters()"
                       placeholder="Cari proyek..."
                       class="w-64 rounded-lg border border-gray-300 bg-white pl-10 pr-4 py-2 text-sm dark:border-gray-700 dark:bg-gray-800 dark:text-white">
                <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
            </div>

            <!-- Toggle View -->
            <div class="flex items-center rounded-lg border border-gray-300 dark:border-gray-700">
                <a href="{{ route('appraisal.projects.index') }}" 
                   class="px-3 py-2 text-sm font-medium rounded-l-lg bg-brand-500 text-white">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17V7m0 10a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2h2a2 2 0 012 2m0 10a2 2 0 002 2h2a2 2 0 002-2M9 7a2 2 0 012-2h2a2 2 0 012 2m0 10V7m0 10a2 2 0 002 2h2a2 2 0 002-2V7a2 2 0 00-2-2h-2a2 2 0 00-2 2" />
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

    <!-- Kanban Board -->
    <div class="flex-1 overflow-x-auto">
        <div class="flex gap-4 h-full pb-4" style="min-width: max-content;">
            @foreach($stages as $stageKey => $stageLabel)
            <div class="flex-shrink-0 w-72 flex flex-col bg-gray-100 dark:bg-gray-800/50 rounded-xl">
                <!-- Column Header -->
                <div class="flex-shrink-0 px-4 py-3 border-b border-gray-200 dark:border-gray-700">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <h3 class="font-semibold text-gray-900 dark:text-white text-sm">{{ $stageLabel }}</h3>
                            <span class="inline-flex items-center justify-center w-6 h-6 text-xs font-medium rounded-full 
                                @if($stageKey === 'done') bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400
                                @elseif(in_array($stageKey, ['lead', 'proposal'])) bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400
                                @elseif(in_array($stageKey, ['contract', 'inspection'])) bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-400
                                @elseif(in_array($stageKey, ['analysis', 'review'])) bg-purple-100 text-purple-700 dark:bg-purple-900/30 dark:text-purple-400
                                @else bg-gray-200 text-gray-700 dark:bg-gray-700 dark:text-gray-300
                                @endif">
                                {{ count($projectsByStage[$stageKey] ?? []) }}
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Column Content -->
                <div class="flex-1 overflow-y-auto p-3 space-y-3"
                     x-data
                     x-init="Sortable.create($el, {
                         group: 'projects',
                         animation: 150,
                         ghostClass: 'opacity-50',
                         dragClass: 'shadow-2xl',
                         onEnd: function(evt) {
                             if (evt.from !== evt.to) {
                                 moveProject(evt.item.dataset.projectId, '{{ $stageKey }}');
                             }
                         }
                     })">
                    @foreach($projectsByStage[$stageKey] ?? [] as $project)
                    @include('appraisal.components.project-card', ['project' => $project])
                    @endforeach
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
        filterPriority: '{{ request('priority') }}',
        filterClient: '{{ request('client_id') }}',
        searchQuery: '{{ request('search') }}',
        
        applyFilters() {
            const params = new URLSearchParams();
            if (this.filterPriority) params.append('priority', this.filterPriority);
            if (this.filterClient) params.append('client_id', this.filterClient);
            if (this.searchQuery) params.append('search', this.searchQuery);
            
            window.location.href = `{{ route('appraisal.projects.index') }}?${params.toString()}`;
        },
        
        moveProject(projectId, newStage) {
            fetch(`/appraisal/projects/${projectId}/move-stage`, {
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
                    // Optional: Show success toast
                } else {
                    // Reload to restore state
                    window.location.reload();
                }
            })
            .catch(error => {
                console.error('Error:', error);
                window.location.reload();
            });
        }
    }
}
</script>
@endpush
@endsection

@extends('layouts.app')

@section('title', 'Kanban Board')

@push('styles')
<style>
    .kanban-board {
        display: flex;
        gap: 0.75rem;
        overflow-x: auto;
        padding-bottom: 1rem;
        min-height: calc(100vh - 200px);
    }
    .kanban-column {
        min-width: 260px;
        max-width: 260px;
        flex-shrink: 0;
    }
    .kanban-cards {
        min-height: 200px;
    }
    .kanban-card {
        cursor: grab;
        transition: transform 0.15s, box-shadow 0.15s;
    }
    .kanban-card:active {
        cursor: grabbing;
    }
    .kanban-card.sortable-ghost {
        opacity: 0.4;
        background: #dbeafe;
    }
    .kanban-card.sortable-chosen {
        transform: rotate(2deg);
        box-shadow: 0 10px 25px -5px rgba(0,0,0,0.2);
    }
    .kanban-column.drag-over {
        background-color: rgba(59, 130, 246, 0.05);
    }
</style>
@endpush

@section('content')
<div>
    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Kanban Board</h1>
            <p class="text-gray-600 dark:text-gray-400 mt-1">Drag & drop untuk memindahkan stage</p>
        </div>
        <div class="flex items-center gap-3">
            {{-- Filter by Project --}}
            <select id="projectFilter" class="px-3 py-2 text-sm rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-900 dark:text-white">
                <option value="">Semua Proyek</option>
                @foreach($projects as $project)
                <option value="{{ $project->id }}" {{ request('project_id') == $project->id ? 'selected' : '' }}>
                    {{ $project->name }}
                </option>
                @endforeach
            </select>
            <a href="{{ route('kanban.assets.index') }}" class="px-3 py-2 text-sm bg-gray-100 hover:bg-gray-200 dark:bg-gray-800 dark:hover:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-lg transition">
                List View
            </a>
            <a href="{{ route('kanban.assets.create') }}" class="px-4 py-2 text-sm bg-brand-500 hover:bg-brand-600 text-white rounded-lg transition">
                + Asset
            </a>
        </div>
    </div>

    {{-- Kanban Board --}}
    <div class="kanban-board">
        @foreach($stages as $stageNum => $stageName)
        <div class="kanban-column bg-gray-50 dark:bg-gray-800/50 rounded-xl">
            {{-- Column Header --}}
            <div class="p-3 border-b border-gray-200 dark:border-gray-700">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <span class="w-6 h-6 rounded-full bg-brand-500 text-white text-xs font-bold flex items-center justify-center">{{ $stageNum }}</span>
                        <h3 class="font-medium text-sm text-gray-900 dark:text-white truncate" title="{{ $stageName }}">{{ Str::limit($stageName, 14) }}</h3>
                    </div>
                    <span class="text-xs font-medium px-2 py-0.5 rounded-full bg-gray-200 dark:bg-gray-700 text-gray-600 dark:text-gray-400">
                        {{ count($assetsByStage[$stageNum]) }}
                    </span>
                </div>
            </div>
            
            {{-- Cards Container --}}
            <div class="kanban-cards p-2 space-y-2" data-stage="{{ $stageNum }}">
                @foreach($assetsByStage[$stageNum] as $asset)
                <div class="kanban-card bg-white dark:bg-gray-900 rounded-lg border border-gray-200 dark:border-gray-700 p-3 shadow-sm" 
                     data-asset-id="{{ $asset->id }}">
                    <div class="flex items-start justify-between mb-2">
                        <a href="{{ route('kanban.assets.show', $asset) }}" class="font-medium text-sm text-gray-900 dark:text-white hover:text-brand-600 dark:hover:text-brand-400 line-clamp-2">
                            {{ $asset->name }}
                        </a>
                        @if($asset->priority === 'critical')
                        <span class="flex-shrink-0 w-2 h-2 rounded-full bg-red-500 animate-pulse" title="Kritikal"></span>
                        @elseif($asset->priority === 'warning')
                        <span class="flex-shrink-0 w-2 h-2 rounded-full bg-yellow-500" title="Warning"></span>
                        @endif
                    </div>
                    <div class="flex items-center justify-between text-xs text-gray-500 dark:text-gray-400">
                        <span>{{ $asset->asset_code }}</span>
                        <span>{{ $asset->project->name ?? '-' }}</span>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endforeach
    </div>
</div>

{{-- Toast Notification --}}
<div id="toast" class="fixed bottom-4 right-4 z-50 hidden">
    <div class="bg-gray-900 dark:bg-white text-white dark:text-gray-900 px-4 py-3 rounded-lg shadow-lg flex items-center gap-3">
        <span id="toastMessage">Asset dipindahkan</span>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const columns = document.querySelectorAll('.kanban-cards');
    const toast = document.getElementById('toast');
    const toastMessage = document.getElementById('toastMessage');

    function showToast(message, isError = false) {
        toastMessage.textContent = message;
        toast.classList.remove('hidden');
        toast.querySelector('div').classList.toggle('bg-red-600', isError);
        toast.querySelector('div').classList.toggle('dark:bg-red-400', isError);
        setTimeout(() => toast.classList.add('hidden'), 3000);
    }

    columns.forEach(column => {
        new Sortable(column, {
            group: 'kanban',
            animation: 150,
            ghostClass: 'sortable-ghost',
            chosenClass: 'sortable-chosen',
            dragClass: 'sortable-drag',
            
            onEnd: function(evt) {
                const assetId = evt.item.dataset.assetId;
                const newStage = evt.to.dataset.stage;
                const oldStage = evt.from.dataset.stage;
                
                if (newStage === oldStage) return;

                // Send update to server
                fetch(`/kanban/assets/${assetId}/move-stage`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ stage: parseInt(newStage) })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showToast(data.message);
                        // Update column counts
                        updateColumnCounts();
                    } else {
                        showToast(data.message || 'Gagal memindahkan', true);
                        // Revert the move
                        evt.from.appendChild(evt.item);
                    }
                })
                .catch(error => {
                    showToast('Terjadi kesalahan', true);
                    evt.from.appendChild(evt.item);
                });
            }
        });
    });

    function updateColumnCounts() {
        columns.forEach(column => {
            const count = column.querySelectorAll('.kanban-card').length;
            const header = column.closest('.kanban-column').querySelector('.rounded-full.bg-gray-200, .rounded-full.bg-gray-700');
            if (header) {
                header.textContent = count;
            }
        });
    }

    // Project filter
    document.getElementById('projectFilter').addEventListener('change', function() {
        const projectId = this.value;
        const url = new URL(window.location.href);
        if (projectId) {
            url.searchParams.set('project_id', projectId);
        } else {
            url.searchParams.delete('project_id');
        }
        window.location.href = url.toString();
    });
});
</script>
@endpush

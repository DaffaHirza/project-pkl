@extends('layouts.app')

@section('title', 'Kanban Board')

@push('styles')
<style>
    .kanban-board {
        display: flex;
        gap: 1rem;
        overflow-x: auto;
        padding-bottom: 1rem;
        min-height: calc(100vh - 200px);
        scroll-behavior: smooth;
    }
    .kanban-board::-webkit-scrollbar {
        height: 10px;
    }
    .kanban-board::-webkit-scrollbar-track {
        background: rgba(0,0,0,0.08);
        border-radius: 10px;
    }
    .kanban-board::-webkit-scrollbar-thumb {
        background: rgba(0,0,0,0.25);
        border-radius: 10px;
        border: 2px solid transparent;
        background-clip: padding-box;
    }
    .kanban-board::-webkit-scrollbar-thumb:hover {
        background: rgba(0,0,0,0.35);
    }
    .kanban-column {
        min-width: 280px;
        max-width: 280px;
        flex-shrink: 0;
    }
    .kanban-cards {
        min-height: 200px;
        max-height: calc(100vh - 320px);
        overflow-y: auto;
    }
    .kanban-cards::-webkit-scrollbar {
        width: 6px;
    }
    .kanban-cards::-webkit-scrollbar-track {
        background: rgba(0,0,0,0.05);
        border-radius: 10px;
    }
    .kanban-cards::-webkit-scrollbar-thumb {
        background: rgba(0,0,0,0.2);
        border-radius: 10px;
    }
    .kanban-cards::-webkit-scrollbar-thumb:hover {
        background: rgba(0,0,0,0.3);
    }
    .kanban-card {
        cursor: grab;
        transition: all 0.2s ease;
    }
    .kanban-card:hover {
        transform: translateY(-2px);
    }
    .kanban-card:active {
        cursor: grabbing;
    }
    .kanban-card.sortable-ghost {
        opacity: 0.5;
        background: #dbeafe;
        border: 2px dashed #3b82f6 !important;
    }
    .kanban-card.sortable-chosen {
        transform: rotate(3deg) scale(1.02);
        box-shadow: 0 20px 25px -5px rgba(0,0,0,0.15), 0 10px 10px -5px rgba(0,0,0,0.1);
        border-color: #3b82f6 !important;
    }
    .kanban-column.drag-over {
        background-color: rgba(59, 130, 246, 0.1);
        border-color: #3b82f6 !important;
    }
    .stage-indicator {
        background: linear-gradient(135deg, var(--tw-gradient-from) 0%, var(--tw-gradient-to) 100%);
    }
</style>
@endpush

@section('content')
<div>
    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div>
            <h1 class="text-xl font-semibold text-gray-900 dark:text-white">Kanban Board</h1>
            <p class="text-sm text-gray-500 dark:text-gray-400">Drag & drop untuk memindahkan stage</p>
        </div>
        <div class="flex items-center gap-3">
            {{-- Filter by Client --}}
            <div class="relative">
                <select id="clientFilter" class="appearance-none pl-3 pr-8 py-2 text-sm rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-brand-500 focus:border-transparent">
                    <option value="">Semua Klien</option>
                    @foreach($clients as $client)
                    <option value="{{ $client->id }}" {{ request('client_id') == $client->id ? 'selected' : '' }}>
                        {{ $client->name }}
                    </option>
                    @endforeach
                </select>
                <svg class="w-4 h-4 absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                </svg>
            </div>
            <a href="{{ route('kanban.assets.index') }}" class="inline-flex items-center gap-2 px-3 py-2 text-sm bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-lg transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16" />
                </svg>
                List
            </a>
            <a href="{{ route('kanban.assets.create') }}" class="inline-flex items-center gap-2 px-3 py-2 text-sm bg-brand-600 hover:bg-brand-700 text-white rounded-lg transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Asset
            </a>
        </div>
    </div>

    {{-- Stage Quick Nav --}}
    <div class="mb-4 flex items-center gap-2 overflow-x-auto pb-2">
        @foreach($stages as $stageNum => $stageName)
        @php $count = count($assetsByStage[$stageNum]); @endphp
        <button onclick="document.querySelector('[data-stage=&quot;{{ $stageNum }}&quot;]').closest('.kanban-column').scrollIntoView({behavior: 'smooth', inline: 'center'})" 
                class="flex-shrink-0 inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-medium transition
                    {{ $count > 0 ? 'bg-brand-50 text-brand-700 dark:bg-brand-900/20 dark:text-brand-400 hover:bg-brand-100 dark:hover:bg-brand-900/30' : 'bg-gray-100 text-gray-500 dark:bg-gray-800 dark:text-gray-400 hover:bg-gray-200 dark:hover:bg-gray-700' }}">
            <span class="w-5 h-5 rounded-full {{ $count > 0 ? 'bg-brand-500 text-white' : 'bg-gray-300 dark:bg-gray-600 text-gray-600 dark:text-gray-400' }} text-[10px] flex items-center justify-center font-bold">{{ $stageNum }}</span>
            <span class="hidden sm:inline">{{ Str::limit($stageName, 8) }}</span>
            @if($count > 0)
            <span class="bg-brand-500 text-white px-1.5 py-0.5 rounded text-[10px] font-bold">{{ $count }}</span>
            @endif
        </button>
        @endforeach
    </div>

    {{-- Kanban Board --}}
    <div class="kanban-board">
        @foreach($stages as $stageNum => $stageName)
        @php 
            $stageColors = [
                1 => 'bg-slate-600',
                2 => 'bg-blue-600',
                3 => 'bg-cyan-600',
                4 => 'bg-teal-600',
                5 => 'bg-emerald-600',
                6 => 'bg-green-600',
                7 => 'bg-lime-600',
                8 => 'bg-yellow-600',
                9 => 'bg-amber-600',
                10 => 'bg-orange-600',
                11 => 'bg-rose-600',
                12 => 'bg-pink-600',
                13 => 'bg-purple-600',
            ];
            $color = $stageColors[$stageNum] ?? 'bg-gray-600';
        @endphp
        <div class="kanban-column bg-white dark:bg-gray-800 rounded-lg border border-gray-300 dark:border-gray-600">
            {{-- Column Header --}}
            <div class="p-3 border-b border-gray-200 dark:border-gray-700">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <div class="w-7 h-7 rounded {{ $color }} text-white text-xs font-medium flex items-center justify-center">{{ $stageNum }}</div>
                        <div>
                            <h3 class="font-medium text-sm text-gray-900 dark:text-white truncate" title="{{ $stageName }}">{{ Str::limit($stageName, 12) }}</h3>
                            <p class="text-xs text-gray-500 dark:text-gray-400">{{ count($assetsByStage[$stageNum]) }} item</p>
                        </div>
                    </div>
                </div>
            </div>
            
            {{-- Cards Container --}}
            <div class="kanban-cards p-2 space-y-2" data-stage="{{ $stageNum }}">
                @foreach($assetsByStage[$stageNum] as $asset)
                <div class="kanban-card bg-gray-50 dark:bg-gray-900 rounded-lg border border-gray-200 dark:border-gray-700 p-3 hover:border-gray-400 dark:hover:border-gray-500 transition" 
                     data-asset-id="{{ $asset->id }}">
                    {{-- Priority indicator --}}
                    @if($asset->priority === 'critical')
                    <div class="absolute -top-1 -right-1 w-2.5 h-2.5 rounded-full bg-red-500"></div>
                    @elseif($asset->priority === 'high')
                    <div class="absolute -top-1 -right-1 w-2.5 h-2.5 rounded-full bg-orange-500"></div>
                    @endif
                    
                    <div class="relative">
                        <a href="{{ route('kanban.assets.show', $asset) }}" class="font-medium text-sm text-gray-900 dark:text-white hover:text-brand-600 dark:hover:text-brand-400 line-clamp-2">
                            {{ $asset->name }}
                        </a>
                        
                        <div class="flex items-center gap-2 mt-2">
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded bg-gray-200 dark:bg-gray-700 text-xs text-gray-600 dark:text-gray-400">
                                {{ Str::limit($asset->client->name ?? '-', 12) }}
                            </span>
                            @if($asset->asset_type)
                            <span class="px-2 py-0.5 rounded bg-gray-200 dark:bg-gray-700 text-xs text-gray-600 dark:text-gray-400">
                                {{ ucfirst(str_replace('_', ' ', Str::limit($asset->asset_type, 8))) }}
                            </span>
                            @endif
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endforeach
    </div>
</div>

{{-- Toast Notification --}}
<div id="toast" class="fixed bottom-6 right-6 z-50 hidden transform transition-all duration-300">
    <div class="bg-gray-900 dark:bg-white text-white dark:text-gray-900 px-5 py-4 rounded-2xl shadow-2xl flex items-center gap-3">
        <div class="w-8 h-8 rounded-full bg-green-500/20 flex items-center justify-center" id="toastIcon">
            <svg class="w-5 h-5 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
            </svg>
        </div>
        <span id="toastMessage" class="font-medium">Asset dipindahkan</span>
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
    const toastIcon = document.getElementById('toastIcon');

    function showToast(message, isError = false) {
        toastMessage.textContent = message;
        if (isError) {
            toastIcon.innerHTML = '<svg class="w-5 h-5 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>';
            toastIcon.className = 'w-8 h-8 rounded-full bg-red-500/20 flex items-center justify-center';
        } else {
            toastIcon.innerHTML = '<svg class="w-5 h-5 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>';
            toastIcon.className = 'w-8 h-8 rounded-full bg-green-500/20 flex items-center justify-center';
        }
        toast.classList.remove('hidden');
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

    // Client filter
    document.getElementById('clientFilter').addEventListener('change', function() {
        const clientId = this.value;
        const url = new URL(window.location.href);
        if (clientId) {
            url.searchParams.set('client_id', clientId);
        } else {
            url.searchParams.delete('client_id');
        }
        window.location.href = url.toString();
    });
});
</script>
@endpush

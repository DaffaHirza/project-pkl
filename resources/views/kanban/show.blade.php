@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gray-50">
    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Header -->
            <div class="flex items-center justify-between mb-8">
                <div class="flex-1">
                    <a href="{{ route('kanban.index') }}" class="text-indigo-600 hover:text-indigo-700 text-sm font-medium mb-3 inline-flex items-center gap-1 transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                        </svg>
                        Kembali
                    </a>
                    <h1 class="text-3xl font-bold text-gray-900">{{ $board->name }}</h1>
                    <p class="text-gray-600 mt-1">{{ $board->description ?? 'Tidak ada deskripsi' }}</p>
                </div>
                <div class="flex gap-2 flex-shrink-0">
                    <button onclick="document.getElementById('addCardModal').showModal()" 
                            class="bg-indigo-600 hover:bg-indigo-700 text-white font-medium py-2 px-4 rounded-lg transition text-sm flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                        </svg>
                        Tambah Tugas
                    </button>
                    <button onclick="document.getElementById('editBoardModal').showModal()" 
                            class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-lg transition text-sm">
                        Edit
                    </button>
                    <form action="{{ route('kanban.destroy', $board) }}" method="POST" class="inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" onclick="return confirm('Yakin ingin menghapus board ini?')"
                                class="bg-red-500 hover:bg-red-600 text-white font-medium py-2 px-4 rounded-lg transition text-sm">
                            Hapus
                        </button>
                    </form>
                </div>
            </div>

            <!-- Success Message -->
            @if (session('success'))
                <div class="mb-6 p-4 bg-green-50 border border-green-200 text-green-800 rounded-lg flex items-start gap-3">
                    <svg class="w-5 h-5 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                    </svg>
                    <p>{{ session('success') }}</p>
                </div>
            @endif

            <!-- Kanban Board -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 pb-8">
                @foreach($board->columns as $column)
                    <div class="flex flex-col bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                        <!-- Column Header -->
                        <div class="bg-gray-50 border-b border-gray-200 px-5 py-4">
                            <h3 class="text-base font-semibold text-gray-900">{{ $column->name }}</h3>
                            <p class="text-xs text-gray-500 mt-1">{{ $column->cards->count() }} tugas</p>
                        </div>

                        <!-- Cards Container (Droppable) -->
                        <div class="flex-1 bg-gray-50 p-4 min-h-64"
                             data-column-id="{{ $column->id }}"
                             ondrop="handleDrop(event)"
                             ondragover="handleDragOver(event)"
                             ondragleave="handleDragLeave(event)">
                            @if($column->cards->count() > 0)
                                <div class="grid grid-cols-3 gap-3">
                                    @foreach($column->cards as $card)
                                        @include('kanban.components.card', ['card' => $card])
                                    @endforeach
                                </div>
                            @else
                                <!-- Empty State -->
                                <div class="text-center text-gray-400 py-12">
                                    <svg class="mx-auto w-8 h-8 mb-2 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                    </svg>
                                    <p class="text-sm">Belum ada tugas</p>
                                </div>
                            @endif
                        </div>

                        
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>

<!-- Include Board and Card Modals -->
@include('kanban.components.board.edit-board')
@include('kanban.components.card.add')

<!-- JavaScript untuk Drag & Drop -->
<script>
    let draggedCard = null;

    function handleDragStart(event, cardId) {
        draggedCard = cardId;
        event.dataTransfer.effectAllowed = 'move';
    }

    function handleDragOver(event) {
        event.preventDefault();
        event.dataTransfer.dropEffect = 'move';
        const column = event.target.closest('[data-column-id]');
        if (column) {
            column.classList.add('bg-blue-50', 'border-2', 'border-indigo-400');
        }
    }

    function handleDragLeave(event) {
        const column = event.target.closest('[data-column-id]');
        if (column && event.target === column) {
            column.classList.remove('bg-blue-50', 'border-2', 'border-indigo-400');
        }
    }

    function handleDrop(event) {
        event.preventDefault();
        const column = event.target.closest('[data-column-id]');
        if (column) {
            column.classList.remove('bg-blue-50', 'border-2', 'border-indigo-400');
        }
        
        if (!draggedCard) return;

        const columnId = column?.dataset.columnId;
        if (!columnId) return;

        // Send request to move card
        fetch(`/cards/${draggedCard}/move`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-Token': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({
                column_id: columnId,
                order: 0
            })
        })
        .then(response => {
            if (response.ok) location.reload();
        })
        .catch(error => console.error('Error:', error));
    }
</script>
@endsection

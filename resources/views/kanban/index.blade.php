@extends('layouts.app')

@section('title', 'Kanban Boards')

@section('content')
<div class="min-h-screen bg-gray-50">
    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Header -->
            <div class="flex items-center justify-between mb-8">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Kanban Boards</h1>
                    <p class="text-gray-600 mt-1">Kelola alur kerja dan tugas tim Anda</p>
                </div>
                <button onclick="document.getElementById('createBoardModal').showModal()" 
                        class="bg-indigo-600 hover:bg-indigo-700 active:bg-indigo-800 text-white font-semibold py-2.5 px-5 rounded-lg transition duration-200 flex items-center gap-2 shadow-sm">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    <span>Board Baru</span>
                </button>
            </div>

            <!-- Display Success Message -->
            @if (session('success'))
                <div class="mb-6 p-4 bg-green-50 border border-green-200 text-green-800 rounded-lg flex items-start gap-3">
                    <svg class="w-5 h-5 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                    </svg>
                    <p>{{ session('success') }}</p>
                </div>
            @endif

            <!-- Boards Grid -->
            @if($boards->count() > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($boards as $board)
                        @include('kanban.components.board', ['board' => $board])
                    @endforeach
                </div>
            @else
                @include('kanban.components.board.empty-state')
            @endif
        </div>
    </div>
</div>

<!-- Include Board Modals -->
@include('kanban.components.board.add-board')

@endsection

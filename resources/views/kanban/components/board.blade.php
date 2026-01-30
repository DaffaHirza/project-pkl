@props(['board'])

<div class="bg-white rounded-lg shadow hover:shadow-md transition duration-200 overflow-hidden border border-gray-100">
    <div class="bg-gradient-to-r from-indigo-500 to-purple-600 h-16"></div>
    <div class="p-5">
        <h3 class="text-lg font-bold text-gray-900 mb-1 truncate">{{ $board->name }}</h3>
        <p class="text-gray-600 text-sm mb-4 line-clamp-2 min-h-10">{{ $board->description ?? 'Tidak ada deskripsi' }}</p>
        
        <!-- Board Stats -->
        <div class="flex items-center justify-between text-sm text-gray-500 mb-5 py-3 border-t border-b border-gray-100">
            <span class="flex items-center gap-1">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 4H5a2 2 0 00-2 2v14a2 2 0 002 2h4m0-18v18m4-18h4a2 2 0 012 2v14a2 2 0 01-2 2h-4m0-18v18"></path>
                </svg>
                {{ $board->columns->count() }} kolom
            </span>
            <span class="flex items-center gap-1">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path>
                </svg>
                {{ $board->cards->count() }} tugas
            </span>
        </div>

        <!-- Action Buttons -->
        <div class="flex gap-2">
            <a href="{{ route('kanban.show', $board) }}" 
               class="flex-1 bg-indigo-600 hover:bg-indigo-700 text-white font-medium py-2 px-3 rounded-lg text-center transition text-sm">
                Buka
            </a>
            <form action="{{ route('kanban.destroy', $board) }}" method="POST" class="flex-1">
                @csrf
                @method('DELETE')
                <button type="submit" onclick="return confirm('Yakin ingin menghapus board ini?')"
                        class="w-full bg-red-50 hover:bg-red-100 text-red-700 font-medium py-2 px-3 rounded-lg transition text-sm border border-red-200">
                    Hapus
                </button>
            </form>
        </div>
    </div>
</div>

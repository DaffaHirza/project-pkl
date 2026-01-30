<!-- Edit Board Modal -->
<dialog id="editBoardModal" class="modal modal-bottom sm:modal-middle">
    <div class="modal-box w-full max-w-md p-0">
        <!-- Close Button -->
        <button onclick="document.getElementById('editBoardModal').close()" 
                class="absolute top-4 right-4 z-10 flex h-8 w-8 items-center justify-center rounded-full bg-gray-100 text-gray-400 hover:bg-gray-200 hover:text-gray-600 dark:bg-white/[0.05] dark:text-gray-400 dark:hover:bg-white/[0.07] dark:hover:text-gray-300 transition">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
        </button>

        <div class="px-6 py-6">
            <!-- Header -->
            <div class="mb-6 pb-4 border-b border-gray-200">
                <h3 class="font-bold text-lg text-gray-900">Edit Board</h3>
            </div>

        <!-- Form -->
        <form action="{{ route('kanban.update', $board) }}" method="POST">
            @csrf
            @method('PATCH')
            
            <!-- Board Name -->
            <div class="mb-5">
                <label class="block text-sm font-semibold text-gray-900 mb-2">Nama Board <span class="text-red-500">*</span></label>
                <input type="text" name="name" value="{{ $board->name }}" required
                       class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition">
            </div>

            <!-- Description -->
            <div class="mb-6">
                <label class="block text-sm font-semibold text-gray-900 mb-2">Deskripsi</label>
                <textarea name="description" rows="3"
                          class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition resize-none">{{ $board->description }}</textarea>
            </div>

            <!-- Action Buttons -->
            <div class="flex gap-3 pt-4 border-t border-gray-200">
                <button type="button" onclick="document.getElementById('editBoardModal').close()" 
                        class="flex-1 px-4 py-2.5 text-gray-700 font-medium border border-gray-300 rounded-lg hover:bg-gray-50 transition">
                    Batal
                </button>
                <button type="submit" class="flex-1 px-4 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-medium rounded-lg transition">
                    Update Board
                </button>
            </div>
        </form>
        </div>
    </div>
</dialog>
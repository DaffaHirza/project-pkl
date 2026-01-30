<!-- Card Detail Modal -->
<dialog id="cardModal{{ $card->id }}" class="modal modal-bottom sm:modal-middle">
    <div class="modal-box w-full max-w-sm p-0">
        <!-- Close Button -->
        <button onclick="document.getElementById('cardModal{{ $card->id }}').close()" 
                class="absolute top-4 right-4 z-10 flex h-8 w-8 items-center justify-center rounded-full bg-gray-100 text-gray-400 hover:bg-gray-200 hover:text-gray-600 dark:bg-white/[0.05] dark:text-gray-400 dark:hover:bg-white/[0.07] dark:hover:text-gray-300 transition">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
        </button>

        <div class="px-6 py-6">
            <!-- Header -->
            <div class="mb-6 pb-4 border-b border-gray-200">
                <h3 class="font-bold text-lg text-gray-900 line-clamp-2">{{ $card->title }}</h3>
            </div>
            
            <!-- Card Details -->
            <div class="space-y-5 mb-6">
                @if($card->description)
                    <div>
                        <p class="text-xs font-semibold text-gray-900 uppercase tracking-wide mb-2">Deskripsi</p>
                        <p class="text-sm text-gray-700 leading-relaxed">{{ $card->description }}</p>
                    </div>
                @endif

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <p class="text-xs font-semibold text-gray-900 uppercase tracking-wide mb-2">Prioritas</p>
                        @if($card->priority === 'high')
                            <span class="inline-block px-2.5 py-1 rounded-md text-xs font-semibold bg-red-100 text-red-700">Tinggi</span>
                        @elseif($card->priority === 'medium')
                            <span class="inline-block px-2.5 py-1 rounded-md text-xs font-semibold bg-yellow-100 text-yellow-700">Sedang</span>
                        @else
                            <span class="inline-block px-2.5 py-1 rounded-md text-xs font-semibold bg-green-100 text-green-700">Rendah</span>
                        @endif
                    </div>
                    @if($card->due_date)
                        <div>
                            <p class="text-xs font-semibold text-gray-900 uppercase tracking-wide mb-2">Tenggat Waktu</p>
                            <p class="text-sm text-gray-700">{{ \Carbon\Carbon::parse($card->due_date)->format('d M Y') }}</p>
                        </div>
                    @endif
                </div>

                <div>
                    <div class="flex items-center justify-between mb-2">
                        <p class="text-xs font-semibold text-gray-900 uppercase tracking-wide">Penugasan</p>
                        <button type="button" onclick="event.stopPropagation(); document.getElementById('assignModal{{ $card->id }}').showModal(); document.getElementById('cardModal{{ $card->id }}').close()"
                                class="text-xs text-indigo-600 hover:text-indigo-700 font-medium transition">
                            Ubah
                        </button>
                    </div>
                    @if($card->assignedUsers->count() > 0)
                        <div class="flex flex-wrap gap-2">
                            @foreach($card->assignedUsers as $user)
                                <span class="inline-flex items-center gap-1.5 bg-indigo-50 text-indigo-700 px-3 py-1.5 rounded-full text-sm font-medium border border-indigo-100">
                                    {{ $user->name }}
                                </span>
                            @endforeach
                        </div>
                    @else
                        <p class="text-sm text-gray-500">Belum ada penugasan</p>
                    @endif
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex gap-2 pt-4 border-t border-gray-200">
                <button type="button" onclick="document.getElementById('editCardModal{{ $card->id }}').showModal(); document.getElementById('cardModal{{ $card->id }}').close()"
                        class="flex-1 px-4 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition text-sm">
                    Edit
                </button>
                <form action="{{ route('cards.destroy', $card) }}" method="POST" class="flex-1">
                    @csrf
                    @method('DELETE')
                    <button type="submit" onclick="return confirm('Yakin ingin menghapus tugas ini?')"
                            class="w-full px-4 py-2.5 bg-red-500 hover:bg-red-600 text-white font-medium rounded-lg transition text-sm">
                        Hapus
                    </button>
                </form>
            </div>
        </div>
    </div>
</dialog>

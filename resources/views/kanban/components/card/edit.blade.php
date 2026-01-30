<!-- Edit Card Modal -->
<dialog id="editCardModal{{ $card->id }}" class="modal modal-bottom sm:modal-middle">
    <div class="modal-box w-full max-w-sm max-h-[90vh] overflow-y-auto p-0">
        <!-- Close Button -->
        <button onclick="document.getElementById('editCardModal{{ $card->id }}').close()" 
                class="absolute top-4 right-4 z-10 flex h-8 w-8 items-center justify-center rounded-full bg-gray-100 text-gray-400 hover:bg-gray-200 hover:text-gray-600 dark:bg-white/[0.05] dark:text-gray-400 dark:hover:bg-white/[0.07] dark:hover:text-gray-300 transition">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
        </button>

        <div class="flex flex-col px-6 overflow-y-auto">
            <!-- Modal Header -->
            <div class="modal-header pt-6 pb-4">
                <h5 class="mb-2 font-semibold text-gray-800 modal-title text-lg lg:text-xl dark:text-white/90">
                    Edit Tugas
                </h5>
                <p class="text-sm text-gray-500 dark:text-gray-400">
                    Perbarui detail tugas Anda
                </p>
            </div>

            <!-- Modal Body -->
            <form action="{{ route('cards.update', $card) }}" method="POST" class="mt-8 modal-body">
                @csrf
                @method('PATCH')
                
                <!-- Task Title -->
                <div class="mb-6">
                    <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                        Judul Tugas <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="title" value="{{ $card->title }}" required
                           class="shadow-theme-xs focus:border-indigo-300 focus:ring-indigo-500/10 dark:focus:border-indigo-800 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30">
                </div>

                <!-- Prioritas -->
                <div class="mb-6">
                    <label class="block mb-3 text-sm font-medium text-gray-700 dark:text-gray-400">
                        Prioritas
                    </label>
                    <div x-data="{ isOptionSelected: false }" class="relative z-20 bg-transparent">
                        <select name="priority"
                                class="shadow-theme-xs focus:border-indigo-300 focus:ring-indigo-500/10 dark:focus:border-indigo-800 h-11 w-full appearance-none rounded-lg border border-gray-300 bg-transparent bg-none px-4 py-2.5 pr-11 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30"
                                :class="isOptionSelected && 'text-gray-800 dark:text-white/90'" @change="isOptionSelected = true">
                            <option value="low" class="text-gray-700 dark:bg-gray-900 dark:text-gray-400" @if($card->priority === 'low') selected @endif>
                                Rendah
                            </option>
                            <option value="medium" class="text-gray-700 dark:bg-gray-900 dark:text-gray-400" @if($card->priority === 'medium') selected @endif>
                                Sedang
                            </option>
                            <option value="high" class="text-gray-700 dark:bg-gray-900 dark:text-gray-400" @if($card->priority === 'high') selected @endif>
                                Tinggi
                            </option>
                        </select>
                        <span class="pointer-events-none absolute top-1/2 right-4 z-30 -translate-y-1/2 text-gray-500 dark:text-gray-400">
                            <svg class="stroke-current" width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M4.79175 7.396L10.0001 12.6043L15.2084 7.396" stroke="" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                        </span>
                    </div>
                </div>

                <!-- Due Date -->
                <div class="mb-6">
                    <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                        Tenggat Waktu
                    </label>
                    <div class="relative">
                        <input type="date" name="due_date" value="{{ $card->due_date ? \Carbon\Carbon::parse($card->due_date)->format('Y-m-d') : '' }}"
                               class="shadow-theme-xs focus:border-indigo-300 focus:ring-indigo-500/10 dark:focus:border-indigo-800 h-11 w-full appearance-none rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30" />
                        <span class="absolute top-1/2 right-3.5 -translate-y-1/2 pointer-events-none">
                            <svg class="fill-gray-700 dark:fill-gray-400" width="14" height="14" viewBox="0 0 14 14" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path fill-rule="evenodd" clip-rule="evenodd" d="M4.33317 0.0830078C4.74738 0.0830078 5.08317 0.418794 5.08317 0.833008V1.24967H8.9165V0.833008C8.9165 0.418794 9.25229 0.0830078 9.6665 0.0830078C10.0807 0.0830078 10.4165 0.418794 10.4165 0.833008V1.24967L11.3332 1.24967C12.2997 1.24967 13.0832 2.03318 13.0832 2.99967V4.99967V11.6663C13.0832 12.6328 12.2997 13.4163 11.3332 13.4163H2.6665C1.70001 13.4163 0.916504 12.6328 0.916504 11.6663V4.99967V2.99967C0.916504 2.03318 1.70001 1.24967 2.6665 1.24967L3.58317 1.24967V0.833008C3.58317 0.418794 3.91896 0.0830078 4.33317 0.0830078ZM4.33317 2.74967H2.6665C2.52843 2.74967 2.4165 2.8616 2.4165 2.99967V4.24967H11.5832V2.99967C11.5832 2.8616 11.4712 2.74967 11.3332 2.74967H9.6665H4.33317ZM11.5832 5.74967H2.4165V11.6663C2.4165 11.8044 2.52843 11.9163 2.6665 11.9163H11.3332C11.4712 11.9163 11.5832 11.8044 11.5832 11.6663V5.74967Z" fill="" />
                            </svg>
                        </span>
                    </div>
                </div>

                <!-- Description -->
                <div class="mb-6">
                    <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                        Deskripsi
                    </label>
                    <textarea name="description" rows="4" placeholder="Tambahkan deskripsi tugas..."
                              class="shadow-theme-xs focus:border-indigo-300 focus:ring-indigo-500/10 dark:focus:border-indigo-800 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 resize-none">{{ $card->description }}</textarea>
                </div>

                <!-- Modal Footer -->
                <div class="flex items-center gap-3 mt-6 modal-footer sm:justify-end pb-6 border-t border-gray-200 pt-6">
                    <button type="button" onclick="document.getElementById('editCardModal{{ $card->id }}').close()" 
                            class="flex w-full justify-center rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50 sm:w-auto dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-white/[0.03]">
                        Batal
                    </button>
                    <button type="submit" class="flex w-full justify-center rounded-lg bg-indigo-600 hover:bg-indigo-700 px-4 py-2.5 text-sm font-medium text-white sm:w-auto">
                        Perbarui Tugas
                    </button>
                </div>
            </form>
        </div>
    </div>
</dialog>

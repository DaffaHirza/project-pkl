<!-- Add Card Modal -->
<dialog id="addCardModal" class="modal modal-bottom sm:modal-middle">
    <div class="modal-box w-full max-w-sm max-h-[90vh] overflow-y-auto p-0">
        <!-- Close Button -->
        <button onclick="document.getElementById('addCardModal').close()" 
                class="absolute top-4 right-4 z-10 flex h-8 w-8 items-center justify-center rounded-full bg-gray-100 text-gray-400 hover:bg-gray-200 hover:text-gray-600 dark:bg-white/[0.05] dark:text-gray-400 dark:hover:bg-white/[0.07] dark:hover:text-gray-300 transition">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
        </button>

        <div class="flex flex-col px-6 overflow-y-auto">
            <!-- Modal Header -->
            <div class="modal-header pt-6 pb-4">
                <h5 class="mb-2 font-semibold text-gray-800 modal-title text-lg lg:text-xl dark:text-white/90">
                    Tambah Tugas Baru
                </h5>
                <p class="text-sm text-gray-500 dark:text-gray-400">
                    Buat tugas baru di board ini
                </p>
            </div>

            <!-- Modal Body -->
            <form action="{{ route('cards.storeFromBoard', $board) }}" method="POST" class="mt-8 modal-body" id="addCardForm" enctype="multipart/form-data">
                @csrf
                
                <!-- Column Selection -->
                <div class="mb-6">
                    <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                        Pilih Kolom <span class="text-red-500">*</span>
                    </label>
                    <div x-data="{ isOptionSelected: false }" class="relative z-20 bg-transparent">
                        <select name="column_id" id="columnSelect" required
                                class="shadow-theme-xs focus:border-indigo-300 focus:ring-indigo-500/10 dark:focus:border-indigo-800 h-11 w-full appearance-none rounded-lg border border-gray-300 bg-transparent bg-none px-4 py-2.5 pr-11 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30"
                                :class="isOptionSelected && 'text-gray-800 dark:text-white/90'" @change="isOptionSelected = true">
                            <option value="">Pilih kolom...</option>
                            @foreach($board->columns as $column)
                                <option value="{{ $column->id }}" class="text-gray-700 dark:bg-gray-900 dark:text-gray-400">
                                    {{ $column->name }}
                                </option>
                            @endforeach
                        </select>
                        <span class="pointer-events-none absolute top-1/2 right-4 z-30 -translate-y-1/2 text-gray-500 dark:text-gray-400">
                            <svg class="stroke-current" width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M4.79175 7.396L10.0001 12.6043L15.2084 7.396" stroke="" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                        </span>
                    </div>
                </div>

                <!-- Task Title -->
                <div class="mb-6">
                    <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                        Judul Tugas <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="title" required placeholder="Masukkan judul tugas..."
                           class="shadow-theme-xs focus:border-indigo-300 focus:ring-indigo-500/10 dark:focus:border-indigo-800 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30">
                </div>

                <!-- Description -->
                <div class="mb-6">
                    <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                        Deskripsi
                    </label>
                    <textarea name="description" rows="4" placeholder="Tambahkan deskripsi tugas..."
                              class="shadow-theme-xs focus:border-indigo-300 focus:ring-indigo-500/10 dark:focus:border-indigo-800 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 resize-none"></textarea>
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
                            <option value="low" class="text-gray-700 dark:bg-gray-900 dark:text-gray-400" selected>
                                Rendah
                            </option>
                            <option value="medium" class="text-gray-700 dark:bg-gray-900 dark:text-gray-400">
                                Sedang
                            </option>
                            <option value="high" class="text-gray-700 dark:bg-gray-900 dark:text-gray-400">
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
                        <input type="date" name="due_date"
                               class="shadow-theme-xs focus:border-indigo-300 focus:ring-indigo-500/10 dark:focus:border-indigo-800 h-11 w-full appearance-none rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30" />
                        <span class="absolute top-1/2 right-3.5 -translate-y-1/2 pointer-events-none">
                            <svg class="fill-gray-700 dark:fill-gray-400" width="14" height="14" viewBox="0 0 14 14" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path fill-rule="evenodd" clip-rule="evenodd" d="M4.33317 0.0830078C4.74738 0.0830078 5.08317 0.418794 5.08317 0.833008V1.24967H8.9165V0.833008C8.9165 0.418794 9.25229 0.0830078 9.6665 0.0830078C10.0807 0.0830078 10.4165 0.418794 10.4165 0.833008V1.24967L11.3332 1.24967C12.2997 1.24967 13.0832 2.03318 13.0832 2.99967V4.99967V11.6663C13.0832 12.6328 12.2997 13.4163 11.3332 13.4163H2.6665C1.70001 13.4163 0.916504 12.6328 0.916504 11.6663V4.99967V2.99967C0.916504 2.03318 1.70001 1.24967 2.6665 1.24967L3.58317 1.24967V0.833008C3.58317 0.418794 3.91896 0.0830078 4.33317 0.0830078ZM4.33317 2.74967H2.6665C2.52843 2.74967 2.4165 2.8616 2.4165 2.99967V4.24967H11.5832V2.99967C11.5832 2.8616 11.4712 2.74967 11.3332 2.74967H9.6665H4.33317ZM11.5832 5.74967H2.4165V11.6663C2.4165 11.8044 2.52843 11.9163 2.6665 11.9163H11.3332C11.4712 11.9163 11.5832 11.8044 11.5832 11.6663V5.74967Z" fill="" />
                            </svg>
                        </span>
                    </div>
                </div>

                <!-- File Upload Dropzone -->
                <div class="mb-6">
                    <label class="mb-3 block text-sm font-medium text-gray-700 dark:text-gray-400">
                        Lampirkan File (Opsional)
                    </label>
                    <div 
                        x-data="{
                            isDragging: false,
                            files: [],
                            fileInput: null,
                            handleDrop(e) {
                                this.isDragging = false;
                                const droppedFiles = Array.from(e.dataTransfer.files);
                                this.handleFiles(droppedFiles);
                            },
                            handleFiles(selectedFiles) {
                                const maxSize = 50 * 1024 * 1024; // 50MB
                                const validFiles = selectedFiles.filter(file => file.size <= maxSize);
                                
                                if (validFiles.length > 0) {
                                    this.files = [...this.files, ...validFiles];
                                    this.updateFileInput();
                                }
                            },
                            updateFileInput() {
                                const dataTransfer = new DataTransfer();
                                this.files.forEach(file => dataTransfer.items.add(file));
                                this.fileInput.files = dataTransfer.files;
                            },
                            removeFile(index) {
                                this.files.splice(index, 1);
                                this.updateFileInput();
                            }
                        }"
                        @drop.prevent="handleDrop($event)"
                        @dragover.prevent="isDragging = true"
                        @dragleave.prevent="isDragging = false"
                        :class="isDragging 
                            ? 'border-indigo-500 bg-indigo-50 dark:bg-indigo-900/20' 
                            : 'border-gray-300 bg-gray-50 dark:border-gray-700 dark:bg-gray-900'"
                        class="dropzone rounded-lg border-2 border-dashed p-6 transition-colors cursor-pointer"
                    >
                        <!-- Hidden File Input -->
                        <input 
                            x-ref="fileInput"
                            @init="fileInput = $ref.fileInput"
                            type="file" 
                            name="attachments[]"
                            @change="handleFiles(Array.from($event.target.files)); $event.target.value = ''"
                            multiple
                            class="hidden"
                            @click.stop
                        />

                        <div class="flex flex-col items-center" @click="$refs.fileInput.click()">
                            <!-- Icon -->
                            <div class="mb-3">
                                <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                </svg>
                            </div>

                            <!-- Text -->
                            <h4 class="mb-1 font-semibold text-gray-700 dark:text-gray-300">
                                <span x-show="!isDragging">Drag files here or click to browse</span>
                                <span x-show="isDragging" x-cloak>Drop files here</span>
                            </h4>
                            <span class="text-xs text-gray-500 dark:text-gray-400">
                                Max file size: 50MB per file
                            </span>
                        </div>

                        <!-- File List -->
                        <div x-show="files.length > 0" class="mt-4 pt-4 border-t border-gray-200 dark:border-gray-700" x-cloak>
                            <div class="space-y-2">
                                <template x-for="(file, index) in files" :key="index">
                                    <div class="flex items-center justify-between p-2 bg-white dark:bg-gray-800 rounded border border-gray-200 dark:border-gray-700">
                                        <div class="flex items-center gap-2 flex-1 min-w-0">
                                            <svg class="w-4 h-4 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                                            <span class="text-xs text-gray-700 dark:text-gray-300 truncate" :title="file.name" x-text="file.name"></span>
                                            <span class="text-xs text-gray-500 dark:text-gray-400 flex-shrink-0" x-text="`(${(file.size / 1024).toFixed(1)}KB)`"></span>
                                        </div>
                                        <button 
                                            @click.stop="removeFile(index)"
                                            type="button"
                                            class="text-red-500 hover:text-red-700 dark:text-red-400 dark:hover:text-red-300 flex-shrink-0"
                                        >
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                            </svg>
                                        </button>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Modal Footer -->
                <div class="flex items-center gap-3 mt-6 modal-footer sm:justify-end pb-6 border-t border-gray-200 pt-6">
                    <button type="button" onclick="document.getElementById('addCardModal').close()" 
                            class="flex w-full justify-center rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50 sm:w-auto dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-white/[0.03]">
                        Batal
                    </button>
                    <button type="submit" class="flex w-full justify-center rounded-lg bg-indigo-600 hover:bg-indigo-700 px-4 py-2.5 text-sm font-medium text-white sm:w-auto">
                        Buat Tugas
                    </button>
                </div>
            </form>
        </div>
    </div>
</dialog>

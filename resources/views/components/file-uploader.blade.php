{{-- 
    Advanced File Uploader Component with Drag & Drop Support
    
    Usage:
    <x-file-uploader 
        :card-id="$card->id"
        :attachments="$card->attachments"
        :max-files="50"
        :max-file-size="100"
    />
--}}

@props([
    'cardId',
    'attachments' => collect(),
    'maxFiles' => 50,
    'maxFileSize' => 100, // in MB
    'acceptedFiles' => 'image/*,application/pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.zip,.rar,.7z,.txt,.csv',
])

<div x-data="fileUploader({
    cardId: {{ $cardId }},
    uploadUrl: '{{ route('attachments.store', ['card' => $cardId]) }}',
    deleteUrl: '{{ url('attachments') }}',
    csrfToken: '{{ csrf_token() }}',
    maxFiles: {{ $maxFiles }},
    maxFileSize: {{ $maxFileSize }},
    existingFiles: {{ $attachments->map(fn($a) => [
        'id' => $a->id,
        'name' => $a->file_name,
        'size' => $a->file_size,
        'sizeHuman' => $a->file_size_human,
        'url' => $a->url,
        'isImage' => $a->isImage(),
        'isPdf' => $a->isPdf(),
        'extension' => $a->extension,
    ])->toJson() }}
})" class="w-full">
    
    {{-- Dropzone Area --}}
    <div 
        x-ref="dropzone"
        @dragover.prevent="isDragging = true"
        @dragleave.prevent="isDragging = false"
        @drop.prevent="handleDrop($event)"
        :class="{ 
            'border-brand-500 bg-brand-50 dark:bg-brand-900/20': isDragging,
            'border-gray-300 dark:border-gray-700 bg-gray-50 dark:bg-gray-800/50': !isDragging 
        }"
        class="relative border-2 border-dashed rounded-xl p-8 transition-all duration-200 text-center cursor-pointer hover:border-brand-400"
        @click="$refs.fileInput.click()">
        
        <input 
            type="file" 
            x-ref="fileInput"
            @change="handleFileSelect($event)"
            multiple
            accept="{{ $acceptedFiles }}"
            class="hidden">
        
        {{-- Upload Icon --}}
        <div class="flex flex-col items-center justify-center">
            <div class="w-16 h-16 mb-4 rounded-full bg-gray-100 dark:bg-gray-700 flex items-center justify-center">
                <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                </svg>
            </div>
            
            <p class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                <span x-show="!isDragging">Drag & drop files atau folder disini, atau <span class="text-brand-500">browse</span></span>
                <span x-show="isDragging" class="text-brand-500">Drop files disini!</span>
            </p>
            <p class="text-xs text-gray-500 dark:text-gray-400">
                Maksimal {{ $maxFiles }} files, masing-masing {{ $maxFileSize }}MB
            </p>
            <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">
                Mendukung: Gambar, PDF, Word, Excel, PowerPoint, ZIP, RAR
            </p>
        </div>
    </div>

    {{-- Upload Progress --}}
    <div x-show="uploadQueue.length > 0" x-cloak class="mt-4 space-y-3">
        <div class="flex items-center justify-between text-sm text-gray-600 dark:text-gray-400">
            <span>Mengupload <span x-text="uploadProgress.completed"></span>/<span x-text="uploadProgress.total"></span> file...</span>
            <span x-text="overallProgress + '%'"></span>
        </div>
        <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2.5">
            <div class="bg-brand-500 h-2.5 rounded-full transition-all duration-300" :style="'width: ' + overallProgress + '%'"></div>
        </div>
        
        {{-- Individual file progress --}}
        <div class="max-h-40 overflow-y-auto space-y-2">
            <template x-for="(item, index) in uploadQueue" :key="index">
                <div class="flex items-center gap-3 p-2 bg-gray-50 dark:bg-gray-800 rounded-lg">
                    <div class="flex-shrink-0">
                        <svg x-show="item.status === 'uploading'" class="w-5 h-5 text-brand-500 animate-spin" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                        </svg>
                        <svg x-show="item.status === 'done'" class="w-5 h-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        <svg x-show="item.status === 'error'" class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-xs font-medium text-gray-700 dark:text-gray-300 truncate" x-text="item.name"></p>
                        <div class="w-full bg-gray-200 dark:bg-gray-600 rounded-full h-1 mt-1">
                            <div class="bg-brand-500 h-1 rounded-full transition-all" :style="'width: ' + item.progress + '%'"></div>
                        </div>
                    </div>
                </div>
            </template>
        </div>
    </div>

    {{-- Uploaded Files Grid --}}
    <div x-show="files.length > 0" x-cloak class="mt-6">
        <div class="flex items-center justify-between mb-3">
            <h4 class="text-sm font-medium text-gray-700 dark:text-gray-300">
                File Terupload (<span x-text="files.length"></span>)
            </h4>
            <div class="flex items-center gap-2">
                <button 
                    x-show="files.length > 0"
                    @click="selectAll()"
                    class="text-xs text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200">
                    <span x-text="selectedFiles.length === files.length ? 'Batal Pilih Semua' : 'Pilih Semua'"></span>
                </button>
                <button 
                    x-show="selectedFiles.length > 0"
                    @click="deleteSelected()"
                    class="text-xs text-red-600 hover:text-red-700 dark:text-red-400 dark:hover:text-red-300 flex items-center gap-1">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>
                    Hapus (<span x-text="selectedFiles.length"></span>)
                </button>
            </div>
        </div>

        {{-- Grid View --}}
        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 xl:grid-cols-6 gap-3">
            <template x-for="file in files" :key="file.id">
                <div 
                    :class="{ 'ring-2 ring-brand-500': selectedFiles.includes(file.id) }"
                    class="group relative bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 overflow-hidden hover:shadow-md transition-all">
                    
                    {{-- Checkbox --}}
                    <div class="absolute top-2 left-2 z-10">
                        <input 
                            type="checkbox" 
                            :checked="selectedFiles.includes(file.id)"
                            @change="toggleSelect(file.id)"
                            class="w-4 h-4 rounded border-gray-300 text-brand-500 focus:ring-brand-500">
                    </div>

                    {{-- Preview --}}
                    <div class="aspect-square relative bg-gray-100 dark:bg-gray-700">
                        <template x-if="file.isImage">
                            <img :src="file.url" :alt="file.name" class="w-full h-full object-cover" loading="lazy">
                        </template>
                        <template x-if="!file.isImage">
                            <div class="w-full h-full flex items-center justify-center">
                                <div class="text-center">
                                    {{-- File type icon --}}
                                    <div class="w-12 h-12 mx-auto mb-2 rounded-lg flex items-center justify-center"
                                         :class="{
                                             'bg-red-100 dark:bg-red-900/30': file.isPdf,
                                             'bg-blue-100 dark:bg-blue-900/30': file.extension === 'doc' || file.extension === 'docx',
                                             'bg-green-100 dark:bg-green-900/30': file.extension === 'xls' || file.extension === 'xlsx',
                                             'bg-orange-100 dark:bg-orange-900/30': file.extension === 'ppt' || file.extension === 'pptx',
                                             'bg-yellow-100 dark:bg-yellow-900/30': file.extension === 'zip' || file.extension === 'rar' || file.extension === '7z',
                                             'bg-gray-100 dark:bg-gray-600': !file.isPdf && !['doc','docx','xls','xlsx','ppt','pptx','zip','rar','7z'].includes(file.extension)
                                         }">
                                        <span class="text-xs font-bold uppercase"
                                              :class="{
                                                  'text-red-600 dark:text-red-400': file.isPdf,
                                                  'text-blue-600 dark:text-blue-400': file.extension === 'doc' || file.extension === 'docx',
                                                  'text-green-600 dark:text-green-400': file.extension === 'xls' || file.extension === 'xlsx',
                                                  'text-orange-600 dark:text-orange-400': file.extension === 'ppt' || file.extension === 'pptx',
                                                  'text-yellow-600 dark:text-yellow-400': file.extension === 'zip' || file.extension === 'rar' || file.extension === '7z',
                                                  'text-gray-500 dark:text-gray-300': !file.isPdf && !['doc','docx','xls','xlsx','ppt','pptx','zip','rar','7z'].includes(file.extension)
                                              }"
                                              x-text="file.extension"></span>
                                    </div>
                                </div>
                            </div>
                        </template>

                        {{-- Hover Actions --}}
                        <div class="absolute inset-0 bg-black/50 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center gap-2">
                            <a :href="file.url" target="_blank" 
                               class="p-2 bg-white rounded-full hover:bg-gray-100 transition-colors"
                               title="Lihat">
                                <svg class="w-4 h-4 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                            </a>
                            <a :href="'/attachments/' + file.id + '/download'" 
                               class="p-2 bg-white rounded-full hover:bg-gray-100 transition-colors"
                               title="Download">
                                <svg class="w-4 h-4 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                                </svg>
                            </a>
                            <button @click="deleteFile(file.id)" 
                                    class="p-2 bg-white rounded-full hover:bg-red-100 transition-colors"
                                    title="Hapus">
                                <svg class="w-4 h-4 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                </svg>
                            </button>
                        </div>
                    </div>

                    {{-- File Info --}}
                    <div class="p-2">
                        <p class="text-xs font-medium text-gray-700 dark:text-gray-300 truncate" x-text="file.name" :title="file.name"></p>
                        <p class="text-xs text-gray-500 dark:text-gray-400" x-text="file.sizeHuman"></p>
                    </div>
                </div>
            </template>
        </div>
    </div>

    {{-- Empty State --}}
    <div x-show="files.length === 0 && uploadQueue.length === 0" x-cloak class="mt-4 text-center py-8">
        <svg class="w-12 h-12 mx-auto text-gray-300 dark:text-gray-600 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
        </svg>
        <p class="text-sm text-gray-500 dark:text-gray-400">Belum ada file yang diupload</p>
    </div>

    {{-- Error Messages --}}
    <div x-show="errors.length > 0" x-cloak class="mt-4 space-y-2">
        <template x-for="(error, index) in errors" :key="index">
            <div class="flex items-center gap-2 p-3 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg text-sm text-red-700 dark:text-red-400">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span class="flex-1"><strong x-text="error.file"></strong>: <span x-text="error.message"></span></span>
                <button @click="errors.splice(index, 1)" class="flex-shrink-0 hover:text-red-800 dark:hover:text-red-300">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </template>
        <button @click="errors = []" x-show="errors.length > 1" class="text-xs text-gray-500 hover:text-gray-700 dark:text-gray-400">
            Hapus semua error
        </button>
    </div>
</div>

@pushOnce('scripts')
<script>
function fileUploader(config) {
    return {
        cardId: config.cardId,
        uploadUrl: config.uploadUrl,
        deleteUrl: config.deleteUrl,
        csrfToken: config.csrfToken,
        maxFiles: config.maxFiles,
        maxFileSize: config.maxFileSize * 1024 * 1024, // Convert to bytes
        
        isDragging: false,
        files: config.existingFiles || [],
        selectedFiles: [],
        uploadQueue: [],
        errors: [],
        overallProgress: 0,
        uploadProgress: { completed: 0, total: 0 },

        handleDrop(event) {
            this.isDragging = false;
            const items = event.dataTransfer.items;
            const filesPromise = [];

            // Handle both files and folder drops using webkitGetAsEntry
            if (items && items.length > 0) {
                for (let i = 0; i < items.length; i++) {
                    const item = items[i];
                    if (item.webkitGetAsEntry) {
                        const entry = item.webkitGetAsEntry();
                        if (entry) {
                            filesPromise.push(this.traverseFileTree(entry));
                        }
                    }
                }

                Promise.all(filesPromise).then(fileArrays => {
                    const allFiles = fileArrays.flat();
                    if (allFiles.length > 0) {
                        this.processFiles(allFiles);
                    }
                });
            } else {
                // Fallback for browsers that don't support webkitGetAsEntry
                this.processFiles(Array.from(event.dataTransfer.files));
            }
        },

        traverseFileTree(item, path = '') {
            return new Promise((resolve) => {
                if (item.isFile) {
                    item.file(file => {
                        resolve([file]);
                    }, () => resolve([]));
                } else if (item.isDirectory) {
                    const dirReader = item.createReader();
                    const allEntries = [];
                    
                    const readEntries = () => {
                        dirReader.readEntries(entries => {
                            if (entries.length === 0) {
                                // Process all entries
                                Promise.all(allEntries.map(entry => 
                                    this.traverseFileTree(entry, path + item.name + '/')
                                )).then(fileArrays => {
                                    resolve(fileArrays.flat());
                                });
                            } else {
                                allEntries.push(...entries);
                                readEntries(); // Continue reading
                            }
                        }, () => resolve([]));
                    };
                    
                    readEntries();
                } else {
                    resolve([]);
                }
            });
        },

        handleFileSelect(event) {
            this.processFiles(Array.from(event.target.files));
            event.target.value = ''; // Reset input
        },

        processFiles(fileList) {
            const validFiles = [];

            for (const file of fileList) {
                // Skip hidden files and system files
                if (file.name.startsWith('.') || file.name === 'Thumbs.db' || file.name === '.DS_Store') {
                    continue;
                }

                // Check max files
                if (this.files.length + validFiles.length >= this.maxFiles) {
                    this.errors.push({
                        file: file.name,
                        message: `Maksimal ${this.maxFiles} files`
                    });
                    continue;
                }

                // Check file size
                if (file.size > this.maxFileSize) {
                    this.errors.push({
                        file: file.name,
                        message: `Ukuran melebihi ${config.maxFileSize}MB`
                    });
                    continue;
                }

                // Check for duplicates
                if (this.files.some(f => f.name === file.name && f.size === file.size)) {
                    this.errors.push({
                        file: file.name,
                        message: 'File sudah ada'
                    });
                    continue;
                }

                validFiles.push(file);
            }

            if (validFiles.length > 0) {
                this.uploadFiles(validFiles);
            }
        },

        async uploadFiles(files) {
            this.uploadQueue = files.map(f => ({ 
                name: f.name, 
                progress: 0, 
                status: 'pending',
                file: f 
            }));
            this.uploadProgress = { completed: 0, total: files.length };
            this.overallProgress = 0;

            // Upload files with concurrency limit (3 at a time)
            const concurrency = 3;
            const chunks = [];
            for (let i = 0; i < this.uploadQueue.length; i += concurrency) {
                chunks.push(this.uploadQueue.slice(i, i + concurrency));
            }

            for (const chunk of chunks) {
                await Promise.all(chunk.map((item, idx) => 
                    this.uploadFile(item, this.uploadQueue.indexOf(item))
                ));
            }

            // Clear queue after all uploads complete
            setTimeout(() => {
                this.uploadQueue = [];
                this.overallProgress = 0;
                this.uploadProgress = { completed: 0, total: 0 };
            }, 1000);
        },

        async uploadFile(item, index) {
            this.uploadQueue[index].status = 'uploading';
            
            const formData = new FormData();
            formData.append('file', item.file);

            try {
                const xhr = new XMLHttpRequest();
                
                // Track upload progress
                xhr.upload.addEventListener('progress', (e) => {
                    if (e.lengthComputable) {
                        this.uploadQueue[index].progress = Math.round((e.loaded / e.total) * 100);
                        this.updateOverallProgress();
                    }
                });

                const response = await new Promise((resolve, reject) => {
                    xhr.open('POST', this.uploadUrl);
                    xhr.setRequestHeader('X-CSRF-TOKEN', this.csrfToken);
                    xhr.setRequestHeader('Accept', 'application/json');
                    
                    xhr.onload = () => {
                        if (xhr.status >= 200 && xhr.status < 300) {
                            resolve(JSON.parse(xhr.responseText));
                        } else {
                            try {
                                reject(JSON.parse(xhr.responseText));
                            } catch {
                                reject({ message: 'Upload gagal' });
                            }
                        }
                    };
                    
                    xhr.onerror = () => reject({ message: 'Network error' });
                    xhr.send(formData);
                });

                if (response.success) {
                    this.uploadQueue[index].status = 'done';
                    this.uploadQueue[index].progress = 100;
                    this.uploadProgress.completed++;
                    
                    this.files.push({
                        id: response.attachment.id,
                        name: response.attachment.file_name,
                        size: response.attachment.file_size,
                        sizeHuman: response.attachment.file_size_human,
                        url: response.attachment.url,
                        isImage: response.attachment.is_image,
                        isPdf: response.attachment.mime_type === 'application/pdf',
                        extension: response.attachment.extension,
                    });
                } else {
                    throw response;
                }
            } catch (error) {
                this.uploadQueue[index].status = 'error';
                this.errors.push({
                    file: item.name,
                    message: error.message || 'Gagal mengupload file'
                });
            }

            this.updateOverallProgress();
        },

        updateOverallProgress() {
            if (this.uploadQueue.length === 0) return;
            const total = this.uploadQueue.reduce((sum, f) => sum + f.progress, 0);
            this.overallProgress = Math.round(total / this.uploadQueue.length);
        },

        selectAll() {
            if (this.selectedFiles.length === this.files.length) {
                this.selectedFiles = [];
            } else {
                this.selectedFiles = this.files.map(f => f.id);
            }
        },

        toggleSelect(fileId) {
            const index = this.selectedFiles.indexOf(fileId);
            if (index === -1) {
                this.selectedFiles.push(fileId);
            } else {
                this.selectedFiles.splice(index, 1);
            }
        },

        async deleteFile(fileId) {
            if (!confirm('Hapus file ini?')) return;

            try {
                const response = await fetch(`${this.deleteUrl}/${fileId}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': this.csrfToken,
                        'Accept': 'application/json',
                    },
                });

                const data = await response.json();

                if (data.success) {
                    this.files = this.files.filter(f => f.id !== fileId);
                    this.selectedFiles = this.selectedFiles.filter(id => id !== fileId);
                }
            } catch (error) {
                console.error('Delete error:', error);
                this.errors.push({ file: '', message: 'Gagal menghapus file' });
            }
        },

        async deleteSelected() {
            if (!confirm(`Hapus ${this.selectedFiles.length} file?`)) return;

            try {
                const response = await fetch(`/cards/${this.cardId}/attachments/bulk-delete`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': this.csrfToken,
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({ attachment_ids: this.selectedFiles }),
                });

                const data = await response.json();

                if (data.success) {
                    this.files = this.files.filter(f => !this.selectedFiles.includes(f.id));
                    this.selectedFiles = [];
                }
            } catch (error) {
                console.error('Bulk delete error:', error);
                this.errors.push({ file: '', message: 'Gagal menghapus files' });
            }
        }
    }
}
</script>
@endPushOnce

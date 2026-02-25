@extends('layouts.app')

@section('title', $asset->name)

@section('content')
<div class="max-w-5xl mx-auto">
    {{-- Back Link --}}
    <a href="{{ route('kanban.assets.index') }}" class="inline-flex items-center gap-2 text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white mb-6">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
        </svg>
        Kembali
    </a>

    {{-- Alert Messages --}}
    @if(session('success'))
    <div class="mb-6 p-4 rounded-lg bg-green-50 border border-green-200 dark:bg-green-900/20 dark:border-green-800">
        <p class="text-green-700 dark:text-green-400">{{ session('success') }}</p>
    </div>
    @endif

    @if(session('error'))
    <div class="mb-6 p-4 rounded-lg bg-red-50 border border-red-200 dark:bg-red-900/20 dark:border-red-800">
        <p class="text-red-700 dark:text-red-400">{{ session('error') }}</p>
    </div>
    @endif

    {{-- Header --}}
    <div class="flex flex-col lg:flex-row lg:items-start lg:justify-between gap-4 mb-6">
        <div>
            <div class="flex items-center gap-3 mb-2">
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">{{ $asset->name }}</h1>
                @if($asset->priority === 'critical')
                <span class="px-2 py-0.5 text-xs font-medium bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400 rounded">Kritikal</span>
                @elseif($asset->priority === 'high')
                <span class="px-2 py-0.5 text-xs font-medium bg-orange-100 text-orange-700 dark:bg-orange-900/30 dark:text-orange-400 rounded">Tinggi</span>
                @endif
            </div>
            <p class="text-gray-600 dark:text-gray-400">{{ $asset->asset_code }} • {{ ucfirst(str_replace('_', ' ', $asset->asset_type)) }}</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('kanban.assets.edit', $asset) }}" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 dark:bg-gray-800 dark:hover:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-lg text-sm font-medium transition">
                Edit Asset
            </a>
        </div>
    </div>

    {{-- Stage Progress --}}
    <div class="bg-white dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-800 p-6 mb-6">
        <div class="flex items-center justify-between mb-4">
            <h2 class="font-semibold text-gray-900 dark:text-white">Progress Stage</h2>
            <span class="text-sm font-medium text-brand-600 dark:text-brand-400">{{ $asset->current_stage }}/13 - {{ $asset->stage_label }}</span>
        </div>
        
        {{-- Stage Bar --}}
        <div class="relative mb-6">
            <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                <div class="bg-brand-500 h-2 rounded-full transition-all duration-500" style="width: {{ ($asset->current_stage / 13) * 100 }}%"></div>
            </div>
            <div class="absolute top-0 left-0 w-full flex justify-between" style="top: -6px;">
                @for($i = 1; $i <= 13; $i++)
                <div class="w-3.5 h-3.5 rounded-full flex items-center justify-center text-[8px] font-bold
                    {{ $i < $asset->current_stage ? 'bg-brand-500 text-white' : ($i == $asset->current_stage ? 'bg-brand-500 text-white ring-2 ring-brand-300' : 'bg-gray-300 dark:bg-gray-600 text-gray-500') }}">
                    {{ $i }}
                </div>
                @endfor
            </div>
        </div>

        {{-- Stage Actions --}}
        <div class="flex flex-wrap gap-2">
            @if($asset->current_stage > 1)
            <form action="{{ route('kanban.assets.move-stage', $asset) }}" method="POST" class="inline">
                @csrf
                <input type="hidden" name="direction" value="prev">
                <button type="submit" class="inline-flex items-center gap-1 px-3 py-1.5 text-sm bg-gray-100 hover:bg-gray-200 dark:bg-gray-800 dark:hover:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-lg transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                    Kembali ke {{ \App\Models\ProjectAssetKanban::STAGES[$asset->current_stage - 1] ?? 'Prev' }}
                </button>
            </form>
            @endif

            @if($asset->current_stage < 13)
            <form action="{{ route('kanban.assets.move-stage', $asset) }}" method="POST" class="inline">
                @csrf
                <input type="hidden" name="direction" value="next">
                <button type="submit" class="inline-flex items-center gap-1 px-3 py-1.5 text-sm bg-brand-500 hover:bg-brand-600 text-white rounded-lg transition">
                    Lanjut ke {{ \App\Models\ProjectAssetKanban::STAGES[$asset->current_stage + 1] ?? 'Next' }}
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </button>
            </form>
            @else
            <span class="px-3 py-1.5 text-sm bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400 rounded-lg font-medium">
                ✓ Selesai (Arsip)
            </span>
            @endif

            {{-- Jump to Stage Dropdown --}}
            <div x-data="{ open: false }" class="relative ml-auto">
                <button @click="open = !open" type="button" 
                    class="inline-flex items-center gap-2 px-3 py-1.5 text-sm border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-lg transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 9l4-4 4 4m0 6l-4 4-4-4"/>
                    </svg>
                    Pindah ke Stage Lain
                </button>
                <div x-show="open" @click.outside="open = false" x-transition
                    class="absolute right-0 mt-2 w-64 bg-white dark:bg-gray-900 rounded-lg shadow-lg border border-gray-200 dark:border-gray-800 py-1 z-50 max-h-80 overflow-y-auto">
                    @foreach(\App\Models\ProjectAssetKanban::STAGES as $num => $label)
                        @if($num != $asset->current_stage)
                        <form action="{{ route('kanban.assets.move-stage', $asset) }}" method="POST">
                            @csrf
                            <input type="hidden" name="stage" value="{{ $num }}">
                            <button type="submit" class="w-full text-left px-4 py-2 text-sm hover:bg-gray-100 dark:hover:bg-gray-800 
                                {{ $num < $asset->current_stage ? 'text-gray-600 dark:text-gray-400' : 'text-gray-900 dark:text-white' }}">
                                <span class="inline-block w-5 text-center font-medium">{{ $num }}</span>
                                {{ $label }}
                                @if($num < $asset->current_stage)
                                    <span class="text-xs text-gray-400">(mundur)</span>
                                @endif
                            </button>
                        </form>
                        @else
                        <div class="px-4 py-2 text-sm bg-brand-50 dark:bg-brand-900/20 text-brand-600 dark:text-brand-400 font-medium">
                            <span class="inline-block w-5 text-center">{{ $num }}</span>
                            {{ $label }} ✓
                        </div>
                        @endif
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Main Info --}}
        <div class="lg:col-span-2 space-y-6">
            {{-- Info Card --}}
            <div class="bg-white dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-800 p-6">
                <h3 class="font-semibold text-gray-900 dark:text-white mb-4">Informasi Asset</h3>
                <dl class="grid grid-cols-2 gap-4 text-sm">
                    <div>
                        <dt class="text-gray-500 dark:text-gray-400">Lokasi</dt>
                        <dd class="text-gray-900 dark:text-white">{{ $asset->location ?: '-' }}</dd>
                    </div>
                    <div>
                        <dt class="text-gray-500 dark:text-gray-400">Proyek</dt>
                        <dd>
                            <a href="{{ route('kanban.projects.show', $asset->project) }}" class="text-brand-600 dark:text-brand-400 hover:underline">
                                {{ $asset->project->name }}
                            </a>
                        </dd>
                    </div>
                    <div>
                        <dt class="text-gray-500 dark:text-gray-400">Klien</dt>
                        <dd>
                            <a href="{{ route('kanban.clients.show', $asset->project->client) }}" class="text-brand-600 dark:text-brand-400 hover:underline">
                                {{ $asset->project->client->name }}
                            </a>
                        </dd>
                    </div>
                </dl>
                @if($asset->description)
                <div class="mt-4 pt-4 border-t border-gray-200 dark:border-gray-800">
                    <dt class="text-gray-500 dark:text-gray-400 text-sm mb-1">Deskripsi</dt>
                    <dd class="text-gray-900 dark:text-white text-sm">{{ $asset->description }}</dd>
                </div>
                @endif
            </div>

            {{-- Documents Section --}}
            <div class="bg-white dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-800 p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="font-semibold text-gray-900 dark:text-white">Dokumen ({{ $asset->documents->count() }})</h3>
                    <button type="button" onclick="document.getElementById('uploadDocModal').classList.remove('hidden')" 
                            class="px-3 py-1.5 text-sm bg-brand-500 hover:bg-brand-600 text-white rounded-lg transition">
                        + Upload File
                    </button>
                </div>

                @if($asset->documents->count() > 0)
                <div class="space-y-2">
                    @foreach($asset->documents->sortByDesc('created_at') as $doc)
                    <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-800 rounded-lg group hover:bg-gray-100 dark:hover:bg-gray-700 transition">
                        <div class="flex items-center gap-3 min-w-0">
                            @php
                                $iconColor = match(strtolower($doc->file_type)) {
                                    'pdf' => 'text-red-500',
                                    'doc', 'docx' => 'text-blue-500',
                                    'xls', 'xlsx' => 'text-green-500',
                                    'jpg', 'jpeg', 'png', 'gif' => 'text-purple-500',
                                    'zip', 'rar' => 'text-yellow-500',
                                    default => 'text-gray-500'
                                };
                            @endphp
                            <span class="flex-shrink-0 w-10 h-10 rounded-lg bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-700 flex items-center justify-center">
                                <svg class="w-5 h-5 {{ $iconColor }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                            </span>
                            <div class="min-w-0 flex-1">
                                <p class="text-sm font-medium text-gray-900 dark:text-white truncate" title="{{ $doc->file_name }}">{{ $doc->file_name }}</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">
                                    <span class="uppercase">{{ $doc->file_type }}</span> • 
                                    {{ number_format($doc->file_size / 1024, 0) }} KB •
                                    Stage {{ $doc->stage }} •
                                    {{ $doc->created_at->diffForHumans() }}
                                </p>
                            </div>
                        </div>
                        <div class="flex items-center gap-1 opacity-50 group-hover:opacity-100 transition">
                            @if($doc->file_path)
                            <a href="{{ route('kanban.documents.download', $doc) }}" class="p-2 text-gray-500 hover:text-brand-500 hover:bg-gray-200 dark:hover:bg-gray-600 rounded-lg transition" title="Download">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                                </svg>
                            </a>
                            <a href="{{ Storage::url($doc->file_path) }}" target="_blank" class="p-2 text-gray-500 hover:text-green-500 hover:bg-gray-200 dark:hover:bg-gray-600 rounded-lg transition" title="Lihat/Preview">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                                </svg>
                            </a>
                            @endif
                            @if(auth()->user()->hasAdminAccess() || $doc->uploaded_by == auth()->id())
                            <form action="{{ route('kanban.documents.destroy', $doc) }}" method="POST" class="inline" onsubmit="return confirm('Hapus dokumen {{ $doc->file_name }}?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="p-2 text-gray-500 hover:text-red-500 hover:bg-gray-200 dark:hover:bg-gray-600 rounded-lg transition" title="Hapus">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                </button>
                            </form>
                            @endif
                        </div>
                    </div>
                    @endforeach
                </div>
                @else
                <div class="text-center py-8">
                    <svg class="w-12 h-12 mx-auto text-gray-300 dark:text-gray-600 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Belum ada dokumen diupload</p>
                    <button type="button" onclick="document.getElementById('uploadDocModal').classList.remove('hidden')" 
                            class="mt-3 text-sm text-brand-500 hover:text-brand-600 font-medium">
                        Upload dokumen pertama
                    </button>
                </div>
                @endif
            </div>

            {{-- Notes Section (only user notes, not system logs) --}}
            @php
                $userNotes = $asset->notes
                    ->filter(fn($n) => $n->type === 'note')
                    ->filter(fn($n) => 
                        !str_starts_with($n->content, 'Upload file:') && 
                        !str_starts_with($n->content, 'Pindah dari') &&
                        !str_starts_with($n->content, 'Hapus file:') &&
                        !str_starts_with($n->content, 'Asset dibuat')
                    );
            @endphp
            <div class="bg-white dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-800 p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="font-semibold text-gray-900 dark:text-white">Catatan Internal ({{ $userNotes->count() }})</h3>
                    <button type="button" onclick="document.getElementById('addNoteModal').classList.remove('hidden')" 
                            class="px-3 py-1.5 text-sm bg-gray-100 hover:bg-gray-200 dark:bg-gray-800 dark:hover:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-lg transition">
                        + Catatan
                    </button>
                </div>

                @if($userNotes->count() > 0)
                <div class="space-y-3">
                    @foreach($userNotes->sortByDesc('created_at') as $note)
                    <div class="p-4 bg-gray-50 dark:bg-gray-800 rounded-lg">
                        <div class="flex items-start justify-between mb-2">
                            <div class="flex items-center gap-2">
                                <span class="text-sm font-medium text-gray-900 dark:text-white">{{ $note->user->name ?? 'System' }}</span>
                                <span class="text-xs px-2 py-0.5 rounded bg-gray-200 dark:bg-gray-700 text-gray-600 dark:text-gray-400">Stage {{ $note->stage }}</span>
                            </div>
                            <span class="text-xs text-gray-500 dark:text-gray-400">{{ $note->created_at->diffForHumans() }}</span>
                        </div>
                        <p class="text-sm text-gray-700 dark:text-gray-300">{{ $note->content }}</p>
                    </div>
                    @endforeach
                </div>
                @else
                <p class="text-sm text-gray-500 dark:text-gray-400 text-center py-8">Belum ada catatan. Tambahkan catatan untuk memberitahu user lain alasan kenapa objek ini belum pindah stage.</p>
                @endif
            </div>
        </div>

        {{-- Sidebar --}}
        <div class="space-y-6">
            {{-- Stage List --}}
            <div class="bg-white dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-800 p-6">
                <h3 class="font-semibold text-gray-900 dark:text-white mb-4">13 Stage</h3>
                <div class="space-y-1">
                    @foreach(\App\Models\ProjectAssetKanban::STAGES as $num => $name)
                    <div class="flex items-center gap-2 p-2 rounded 
                        {{ $num == $asset->current_stage ? 'bg-brand-50 dark:bg-brand-900/20' : '' }}">
                        @if($num < $asset->current_stage)
                        <span class="w-5 h-5 rounded-full bg-green-500 flex items-center justify-center text-white text-xs">✓</span>
                        @elseif($num == $asset->current_stage)
                        <span class="w-5 h-5 rounded-full bg-brand-500 flex items-center justify-center text-white text-xs">{{ $num }}</span>
                        @else
                        <span class="w-5 h-5 rounded-full bg-gray-200 dark:bg-gray-700 flex items-center justify-center text-gray-500 dark:text-gray-400 text-xs">{{ $num }}</span>
                        @endif
                        <span class="text-xs {{ $num == $asset->current_stage ? 'text-brand-700 dark:text-brand-400 font-medium' : 'text-gray-600 dark:text-gray-400' }}">{{ $name }}</span>
                    </div>
                    @endforeach
                </div>
            </div>

            {{-- Meta --}}
            <div class="bg-white dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-800 p-6">
                <h3 class="font-semibold text-gray-900 dark:text-white mb-4">Info Lainnya</h3>
                <dl class="space-y-3 text-sm">
                    <div class="flex justify-between">
                        <dt class="text-gray-500 dark:text-gray-400">Dibuat</dt>
                        <dd class="text-gray-900 dark:text-white">{{ $asset->created_at->format('d M Y') }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-gray-500 dark:text-gray-400">Diperbarui</dt>
                        <dd class="text-gray-900 dark:text-white">{{ $asset->updated_at->diffForHumans() }}</dd>
                    </div>
                </dl>
            </div>
        </div>
    </div>
</div>

{{-- Upload Document Modal --}}
<div id="uploadDocModal" class="hidden fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4">
    <div class="bg-white dark:bg-gray-900 rounded-xl w-full max-w-lg p-6">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Upload Dokumen</h3>
            <button onclick="document.getElementById('uploadDocModal').classList.add('hidden')" class="text-gray-400 hover:text-gray-500">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
        <form id="uploadForm" action="{{ route('kanban.documents.store', $asset) }}" method="POST" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="stage" value="{{ $asset->current_stage }}">
            
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Pilih File (bisa pilih beberapa) *</label>
                <div class="border-2 border-dashed border-gray-300 dark:border-gray-700 rounded-lg p-6 text-center hover:border-brand-500 transition cursor-pointer" onclick="document.getElementById('fileInput').click()">
                    <svg class="w-10 h-10 mx-auto text-gray-400 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                    </svg>
                    <p class="text-sm text-gray-600 dark:text-gray-400">Klik untuk memilih file atau drag & drop</p>
                    <p class="text-xs text-gray-500 dark:text-gray-500 mt-1">PDF, DOC, DOCX, XLS, XLSX, JPG, PNG, ZIP</p>
                </div>
                <input type="file" id="fileInput" name="files[]" multiple class="hidden" onchange="updateFileList(this)">
                <div id="fileList" class="mt-3 space-y-2 hidden"></div>
            </div>
            
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Catatan (opsional)</label>
                <textarea name="description" rows="2" class="w-full px-4 py-2 rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-900 dark:text-white" placeholder="Deskripsi dokumen..."></textarea>
            </div>
            
            <div class="flex gap-3">
                <button type="submit" class="flex-1 py-2.5 bg-brand-500 hover:bg-brand-600 text-white rounded-lg font-medium transition">Upload</button>
                <button type="button" onclick="document.getElementById('uploadDocModal').classList.add('hidden')" class="px-4 py-2.5 text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white transition">Batal</button>
            </div>
        </form>
    </div>
</div>

<script>
function updateFileList(input) {
    const fileList = document.getElementById('fileList');
    fileList.innerHTML = '';
    if (input.files.length > 0) {
        fileList.classList.remove('hidden');
        for (let i = 0; i < input.files.length; i++) {
            const file = input.files[i];
            const size = (file.size / 1024 / 1024).toFixed(2);
            fileList.innerHTML += `
                <div class="flex items-center justify-between p-2 bg-gray-50 dark:bg-gray-800 rounded-lg text-sm">
                    <span class="truncate text-gray-700 dark:text-gray-300">${file.name}</span>
                    <span class="text-gray-500 dark:text-gray-400 ml-2 flex-shrink-0">${size} MB</span>
                </div>
            `;
        }
    } else {
        fileList.classList.add('hidden');
    }
}

// Handle upload form submission with loading state
document.getElementById('uploadForm')?.addEventListener('submit', function(e) {
    const btn = this.querySelector('button[type="submit"]');
    const originalText = btn.innerHTML;
    btn.disabled = true;
    btn.innerHTML = `
        <svg class="animate-spin h-5 w-5 mr-2 inline" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
        </svg>
        Mengupload...
    `;
});
</script>

{{-- Add Note Modal --}}
<div id="addNoteModal" class="hidden fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4">
    <div class="bg-white dark:bg-gray-900 rounded-xl w-full max-w-md p-6">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Tambah Catatan</h3>
            <button onclick="document.getElementById('addNoteModal').classList.add('hidden')" class="text-gray-400 hover:text-gray-500">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
        <form action="{{ route('kanban.notes.store', $asset) }}" method="POST">
            @csrf
            <input type="hidden" name="stage" value="{{ $asset->current_stage }}">
            
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Catatan *</label>
                <textarea name="content" rows="4" required class="w-full px-4 py-2 rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-900 dark:text-white" placeholder="Tulis catatan..."></textarea>
            </div>
            
            <div class="flex gap-3">
                <button type="submit" class="flex-1 py-2.5 bg-brand-500 hover:bg-brand-600 text-white rounded-lg font-medium transition">Simpan</button>
                <button type="button" onclick="document.getElementById('addNoteModal').classList.add('hidden')" class="px-4 py-2.5 text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white transition">Batal</button>
            </div>
        </form>
    </div>
</div>
@endsection

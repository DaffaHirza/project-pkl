@extends('layouts.app')

@section('title', $asset->name . ' - Detail Objek')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
        <div>
            <div class="flex items-center gap-3 mb-2">
                <a href="{{ route('appraisal.assets.index') }}" 
                   class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                </a>
                <span class="font-mono text-sm text-gray-500 dark:text-gray-400">{{ $asset->asset_code }}</span>
                <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium
                    @if($asset->priority_status === 'critical') bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400
                    @elseif($asset->priority_status === 'warning') bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-400
                    @else bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400
                    @endif">
                    {{ ucfirst($asset->priority_status) }}
                </span>
            </div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">{{ $asset->name }}</h1>
            <div class="mt-2 flex flex-wrap items-center gap-3 text-sm text-gray-500 dark:text-gray-400">
                <span class="flex items-center gap-1">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    <a href="{{ route('appraisal.projects.show', $asset->project) }}" class="hover:text-brand-500">
                        {{ $asset->project->name }}
                    </a>
                </span>
                <span>•</span>
                <span class="flex items-center gap-1">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                    {{ $asset->project->client->name ?? 'No Client' }}
                </span>
            </div>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('appraisal.assets.edit', $asset) }}" 
               class="inline-flex items-center gap-2 rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-300 dark:hover:bg-gray-700">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                </svg>
                Edit
            </a>
        </div>
    </div>

    <!-- Stage Progress -->
    <div class="rounded-xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-gray-900">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Progress Workflow</h2>
            <span class="text-sm font-medium text-brand-500">{{ $asset->progress_percentage }}% selesai</span>
        </div>
        
        <!-- Progress Bar -->
        <div class="w-full bg-gray-200 rounded-full h-2 mb-6 dark:bg-gray-700">
            <div class="bg-brand-500 h-2 rounded-full transition-all duration-300" style="width: {{ $asset->progress_percentage }}%"></div>
        </div>

        <!-- Stage Steps -->
        <div class="flex justify-between overflow-x-auto pb-2">
            @foreach($stages as $stageKey => $stageLabel)
                @php
                    $stageKeys = array_keys($stages);
                    $currentIndex = array_search($asset->current_stage, $stageKeys);
                    $thisIndex = array_search($stageKey, $stageKeys);
                    $isCompleted = $thisIndex < $currentIndex;
                    $isCurrent = $stageKey === $asset->current_stage;
                @endphp
                <div class="flex flex-col items-center min-w-[80px] px-2">
                    <div class="flex items-center justify-center w-10 h-10 rounded-full mb-2
                        @if($isCompleted) bg-green-500 text-white
                        @elseif($isCurrent) bg-brand-500 text-white ring-4 ring-brand-100 dark:ring-brand-900/30
                        @else bg-gray-200 text-gray-500 dark:bg-gray-700 dark:text-gray-400
                        @endif">
                        @if($isCompleted)
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                        @else
                            <span class="text-sm font-medium">{{ $thisIndex + 1 }}</span>
                        @endif
                    </div>
                    <span class="text-xs text-center font-medium
                        @if($isCurrent) text-brand-600 dark:text-brand-400
                        @elseif($isCompleted) text-green-600 dark:text-green-400
                        @else text-gray-500 dark:text-gray-400
                        @endif">
                        {{ $stageLabel }}
                    </span>
                </div>
            @endforeach
        </div>

        <!-- Stage Actions -->
        @if($asset->current_stage !== 'arsip')
        <div class="mt-6 pt-4 border-t border-gray-200 dark:border-gray-700">
            <form action="{{ route('appraisal.assets.move-stage', $asset) }}" method="POST" class="flex items-center gap-4">
                @csrf
                <label class="text-sm font-medium text-gray-700 dark:text-gray-300">Pindahkan ke:</label>
                <select name="stage" class="flex-1 max-w-xs rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white text-sm">
                    @foreach($stages as $stageKey => $stageLabel)
                        @if($stageKey !== $asset->current_stage)
                            <option value="{{ $stageKey }}">{{ $stageLabel }}</option>
                        @endif
                    @endforeach
                </select>
                <button type="submit" class="px-4 py-2 bg-brand-500 hover:bg-brand-600 text-white text-sm font-medium rounded-lg transition-colors">
                    Pindah Stage
                </button>
            </form>
        </div>
        @endif
    </div>

    <div class="grid grid-cols-1 gap-6 xl:grid-cols-3">
        <!-- Main Info -->
        <div class="xl:col-span-2 space-y-6">
            <!-- Asset Details -->
            <div class="rounded-xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900">
                <div class="border-b border-gray-200 dark:border-gray-700 px-6 py-4">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Detail Objek</h2>
                </div>
                <div class="p-6">
                    <dl class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                        <div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Jenis Aset</dt>
                            <dd class="mt-1 text-sm text-gray-900 dark:text-white">
                                {{ \App\Models\ProjectAsset::ASSET_TYPES[$asset->asset_type] ?? $asset->asset_type }}
                            </dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Lokasi</dt>
                            <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ $asset->location_address ?? '-' }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Target Selesai</dt>
                            <dd class="mt-1 text-sm text-gray-900 dark:text-white">
                                @if($asset->target_completion_date)
                                    <span class="{{ $asset->target_completion_date->isPast() ? 'text-red-600 dark:text-red-400' : '' }}">
                                        {{ $asset->target_completion_date->format('d F Y') }}
                                        @if($asset->target_completion_date->isPast())
                                            (Terlambat)
                                        @endif
                                    </span>
                                @else
                                    -
                                @endif
                            </dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Dibuat</dt>
                            <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ $asset->created_at->format('d F Y H:i') }}</dd>
                        </div>
                        @if($asset->description)
                        <div class="sm:col-span-2">
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Deskripsi</dt>
                            <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ $asset->description }}</dd>
                        </div>
                        @endif
                    </dl>
                </div>
            </div>

            <!-- Notes Section -->
            <div class="rounded-xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900" x-data="{ editing: false }">
                <div class="border-b border-gray-200 dark:border-gray-700 px-6 py-4 flex items-center justify-between">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Catatan</h2>
                    <button @click="editing = !editing" 
                            class="text-sm text-brand-500 hover:text-brand-600 flex items-center gap-1">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                        </svg>
                        <span x-text="editing ? 'Batal' : 'Edit'"></span>
                    </button>
                </div>
                <div class="p-6">
                    <div x-show="!editing">
                        @if($asset->notes)
                            <p class="text-sm text-gray-900 dark:text-white whitespace-pre-wrap">{{ $asset->notes }}</p>
                        @else
                            <p class="text-sm text-gray-500 dark:text-gray-400 italic">Belum ada catatan</p>
                        @endif
                    </div>
                    <form x-show="editing" action="{{ route('appraisal.assets.update-notes', $asset) }}" method="POST">
                        @csrf
                        @method('PATCH')
                        <textarea name="notes" rows="6" 
                                  class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white text-sm focus:ring-brand-500 focus:border-brand-500"
                                  placeholder="Tambahkan catatan untuk objek ini...">{{ $asset->notes }}</textarea>
                        <div class="flex justify-end gap-2 mt-3">
                            <button type="button" @click="editing = false" 
                                    class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg">
                                Batal
                            </button>
                            <button type="submit" 
                                    class="px-4 py-2 bg-brand-500 hover:bg-brand-600 text-white text-sm font-medium rounded-lg">
                                Simpan Catatan
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Documents Section -->
            <div class="rounded-xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900" x-data="{ showUpload: false }">
                <div class="border-b border-gray-200 dark:border-gray-700 px-6 py-4 flex items-center justify-between">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Lampiran Dokumen</h2>
                    <button @click="showUpload = !showUpload" 
                            class="text-sm text-brand-500 hover:text-brand-600 flex items-center gap-1">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        Upload Dokumen
                    </button>
                </div>
                
                <!-- Upload Form -->
                <div x-show="showUpload" x-cloak class="border-b border-gray-200 dark:border-gray-700 p-6 bg-gray-50 dark:bg-gray-800/50">
                    <form action="{{ route('appraisal.assets.upload-document', $asset) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">File (Max 100MB)</label>
                                <input type="file" name="file" required
                                       class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-brand-50 file:text-brand-700 hover:file:bg-brand-100 dark:file:bg-brand-900/30 dark:file:text-brand-400">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Kategori</label>
                                <select name="category" required
                                        class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white text-sm">
                                    @foreach(\App\Models\DocumentKanban::CATEGORIES as $key => $label)
                                        <option value="{{ $key }}">{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Deskripsi (Opsional)</label>
                                <input type="text" name="description" 
                                       class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white text-sm"
                                       placeholder="Deskripsi singkat tentang dokumen...">
                            </div>
                        </div>
                        <div class="flex justify-end gap-2 mt-4">
                            <button type="button" @click="showUpload = false" 
                                    class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg">
                                Batal
                            </button>
                            <button type="submit" 
                                    class="px-4 py-2 bg-brand-500 hover:bg-brand-600 text-white text-sm font-medium rounded-lg flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                                </svg>
                                Upload
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Documents List -->
                <div class="divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse($asset->documents as $document)
                    <div class="p-4 flex items-center justify-between hover:bg-gray-50 dark:hover:bg-gray-800/50">
                        <div class="flex items-center gap-3 min-w-0 flex-1">
                            <div class="flex-shrink-0 w-10 h-10 rounded-lg bg-gray-100 dark:bg-gray-800 flex items-center justify-center">
                                @if(str_contains($document->file_type, 'image'))
                                    <svg class="w-5 h-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                @elseif(str_contains($document->file_type, 'pdf'))
                                    <svg class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                                    </svg>
                                @else
                                    <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                    </svg>
                                @endif
                            </div>
                            <div class="min-w-0 flex-1">
                                <p class="text-sm font-medium text-gray-900 dark:text-white truncate">{{ $document->file_name }}</p>
                                <div class="flex items-center gap-2 text-xs text-gray-500 dark:text-gray-400">
                                    <span class="inline-flex items-center rounded-full px-2 py-0.5 bg-gray-100 dark:bg-gray-800 text-gray-600 dark:text-gray-400">
                                        {{ $document->category_label }}
                                    </span>
                                    <span>{{ $document->file_size_human }}</span>
                                    <span>•</span>
                                    <span>{{ $document->created_at->diffForHumans() }}</span>
                                    @if($document->uploader)
                                        <span>•</span>
                                        <span>{{ $document->uploader->name }}</span>
                                    @endif
                                </div>
                                @if($document->description)
                                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ $document->description }}</p>
                                @endif
                            </div>
                        </div>
                        <div class="flex items-center gap-2 flex-shrink-0 ml-4">
                            <a href="{{ route('appraisal.assets.download-document', [$asset, $document]) }}" 
                               class="p-2 text-gray-500 hover:text-brand-500 hover:bg-gray-100 dark:hover:bg-gray-800 rounded-lg"
                               title="Download">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                                </svg>
                            </a>
                            <form action="{{ route('appraisal.assets.delete-document', [$asset, $document]) }}" method="POST" 
                                  onsubmit="return confirm('Hapus dokumen ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" 
                                        class="p-2 text-gray-500 hover:text-red-500 hover:bg-gray-100 dark:hover:bg-gray-800 rounded-lg"
                                        title="Hapus">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                </button>
                            </form>
                        </div>
                    </div>
                    @empty
                    <div class="p-8 text-center text-gray-500 dark:text-gray-400">
                        <svg class="w-12 h-12 mx-auto text-gray-400 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                        </svg>
                        <p class="text-sm">Belum ada dokumen</p>
                        <p class="text-xs mt-1">Klik "Upload Dokumen" untuk menambahkan lampiran</p>
                    </div>
                    @endforelse
                </div>
            </div>

            <!-- Inspections -->
            <div class="rounded-xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900">
                <div class="border-b border-gray-200 dark:border-gray-700 px-6 py-4 flex items-center justify-between">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Inspeksi</h2>
                    @if(in_array($asset->current_stage, ['inisiasi', 'eksekusi_lapangan']))
                    <a href="{{ route('appraisal.inspections.create', ['asset' => $asset->id]) }}" 
                       class="text-sm text-brand-500 hover:text-brand-600">+ Tambah Inspeksi</a>
                    @endif
                </div>
                <div class="divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse($asset->inspections as $inspection)
                    <div class="p-6">
                        <div class="flex items-start justify-between">
                            <div>
                                <p class="font-medium text-gray-900 dark:text-white">{{ $inspection->inspection_date->format('d F Y') }}</p>
                                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Surveyor: {{ $inspection->surveyor->name ?? '-' }}</p>
                                @if($inspection->notes)
                                <p class="text-sm text-gray-600 dark:text-gray-300 mt-2">{{ Str::limit($inspection->notes, 150) }}</p>
                                @endif
                            </div>
                            <span class="inline-flex items-center rounded-full px-2 py-0.5 text-xs font-medium
                                @if($inspection->status === 'completed') bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400
                                @elseif($inspection->status === 'scheduled') bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400
                                @else bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300
                                @endif">
                                {{ ucfirst($inspection->status) }}
                            </span>
                        </div>
                    </div>
                    @empty
                    <div class="p-6 text-center text-gray-500 dark:text-gray-400">
                        <p>Belum ada inspeksi</p>
                    </div>
                    @endforelse
                </div>
            </div>

            <!-- Reports -->
            <div class="rounded-xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900">
                <div class="border-b border-gray-200 dark:border-gray-700 px-6 py-4 flex items-center justify-between">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Laporan</h2>
                    @if(in_array($asset->current_stage, ['analisis', 'drafting', 'review_internal', 'approval_klien', 'finalisasi']))
                    <a href="{{ route('appraisal.reports.create', ['asset' => $asset->id]) }}" 
                       class="text-sm text-brand-500 hover:text-brand-600">+ Tambah Laporan</a>
                    @endif
                </div>
                <div class="divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse($asset->reports as $report)
                    <div class="p-6">
                        <div class="flex items-start justify-between">
                            <div>
                                <a href="{{ route('appraisal.reports.show', $report) }}" 
                                   class="font-medium text-gray-900 dark:text-white hover:text-brand-500">
                                    {{ $report->title ?? 'Laporan #' . $report->id }}
                                </a>
                                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">{{ $report->created_at->format('d F Y') }}</p>
                            </div>
                            <span class="inline-flex items-center rounded-full px-2 py-0.5 text-xs font-medium
                                @if($report->status === 'approved') bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400
                                @elseif($report->status === 'revision') bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-400
                                @else bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300
                                @endif">
                                {{ ucfirst($report->status) }}
                            </span>
                        </div>
                    </div>
                    @empty
                    <div class="p-6 text-center text-gray-500 dark:text-gray-400">
                        <p>Belum ada laporan</p>
                    </div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Project Info Card -->
            <div class="rounded-xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900">
                <div class="border-b border-gray-200 dark:border-gray-700 px-6 py-4">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Info Proyek</h2>
                </div>
                <div class="p-6">
                    <div class="space-y-4">
                        <div>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Kode Proyek</p>
                            <p class="font-medium text-gray-900 dark:text-white">{{ $asset->project->project_code }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Nama Proyek</p>
                            <a href="{{ route('appraisal.projects.show', $asset->project) }}" 
                               class="font-medium text-brand-500 hover:text-brand-600">
                                {{ $asset->project->name }}
                            </a>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Klien</p>
                            <p class="font-medium text-gray-900 dark:text-white">{{ $asset->project->client->name ?? '-' }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Total Objek dalam Proyek</p>
                            <p class="font-medium text-gray-900 dark:text-white">{{ $asset->project->assets()->count() }} objek</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Activities -->
            <div class="rounded-xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900">
                <div class="border-b border-gray-200 dark:border-gray-700 px-6 py-4">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Aktivitas Terbaru</h2>
                </div>
                <div class="divide-y divide-gray-200 dark:divide-gray-700 max-h-80 overflow-y-auto">
                    @forelse($asset->activities as $activity)
                    <div class="px-6 py-4">
                        <p class="text-sm text-gray-900 dark:text-white">{{ $activity->description }}</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                            {{ $activity->user->name ?? 'System' }} • {{ $activity->created_at->diffForHumans() }}
                        </p>
                    </div>
                    @empty
                    <div class="px-6 py-8 text-center text-gray-500 dark:text-gray-400">
                        <p class="text-sm">Belum ada aktivitas</p>
                    </div>
                    @endforelse
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="rounded-xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900">
                <div class="border-b border-gray-200 dark:border-gray-700 px-6 py-4">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Aksi Cepat</h2>
                </div>
                <div class="p-4 space-y-2">
                    @if(in_array($asset->current_stage, ['inisiasi', 'eksekusi_lapangan']))
                    <a href="{{ route('appraisal.inspections.create', ['asset' => $asset->id]) }}" 
                       class="flex items-center gap-3 w-full px-4 py-3 text-left text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800 rounded-lg transition-colors">
                        <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                        </svg>
                        Jadwalkan Inspeksi
                    </a>
                    @endif
                    
                    @if(in_array($asset->current_stage, ['eksekusi_lapangan', 'analisis']))
                    <a href="{{ route('appraisal.working-papers.create', ['asset' => $asset->id]) }}" 
                       class="flex items-center gap-3 w-full px-4 py-3 text-left text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800 rounded-lg transition-colors">
                        <svg class="w-5 h-5 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        Buat Kertas Kerja
                    </a>
                    @endif

                    @if(in_array($asset->current_stage, ['analisis', 'drafting', 'review_internal', 'approval_klien', 'finalisasi']))
                    <a href="{{ route('appraisal.reports.create', ['asset' => $asset->id]) }}" 
                       class="flex items-center gap-3 w-full px-4 py-3 text-left text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800 rounded-lg transition-colors">
                        <svg class="w-5 h-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        Buat Laporan
                    </a>
                    @endif

                    <button type="button" onclick="updatePriority()" 
                            class="flex items-center gap-3 w-full px-4 py-3 text-left text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800 rounded-lg transition-colors">
                        <svg class="w-5 h-5 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                        Ubah Prioritas
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Priority Modal -->
<div id="priorityModal" class="fixed inset-0 z-50 hidden overflow-y-auto">
    <div class="flex min-h-screen items-center justify-center p-4">
        <div class="fixed inset-0 bg-black/50" onclick="closePriorityModal()"></div>
        <div class="relative w-full max-w-sm rounded-xl bg-white dark:bg-gray-800 shadow-xl">
            <div class="border-b border-gray-200 dark:border-gray-700 px-6 py-4">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Ubah Prioritas</h3>
            </div>
            <form action="{{ route('appraisal.assets.update-priority', $asset) }}" method="POST" class="p-6">
                @csrf
                <div class="space-y-3">
                    <label class="flex items-center gap-3 p-3 rounded-lg border border-gray-200 dark:border-gray-700 cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-700">
                        <input type="radio" name="priority" value="normal" {{ $asset->priority_status === 'normal' ? 'checked' : '' }} 
                               class="text-green-500 focus:ring-green-500">
                        <span class="flex items-center gap-2">
                            <span class="w-3 h-3 rounded-full bg-green-500"></span>
                            Normal
                        </span>
                    </label>
                    <label class="flex items-center gap-3 p-3 rounded-lg border border-gray-200 dark:border-gray-700 cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-700">
                        <input type="radio" name="priority" value="warning" {{ $asset->priority_status === 'warning' ? 'checked' : '' }}
                               class="text-yellow-500 focus:ring-yellow-500">
                        <span class="flex items-center gap-2">
                            <span class="w-3 h-3 rounded-full bg-yellow-500"></span>
                            Warning
                        </span>
                    </label>
                    <label class="flex items-center gap-3 p-3 rounded-lg border border-gray-200 dark:border-gray-700 cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-700">
                        <input type="radio" name="priority" value="critical" {{ $asset->priority_status === 'critical' ? 'checked' : '' }}
                               class="text-red-500 focus:ring-red-500">
                        <span class="flex items-center gap-2">
                            <span class="w-3 h-3 rounded-full bg-red-500"></span>
                            Critical
                        </span>
                    </label>
                </div>
                <div class="flex justify-end gap-3 mt-6">
                    <button type="button" onclick="closePriorityModal()" 
                            class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300">
                        Batal
                    </button>
                    <button type="submit" 
                            class="px-4 py-2 bg-brand-500 hover:bg-brand-600 text-white text-sm font-medium rounded-lg">
                        Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function updatePriority() {
        document.getElementById('priorityModal').classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }

    function closePriorityModal() {
        document.getElementById('priorityModal').classList.add('hidden');
        document.body.style.overflow = '';
    }

    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closePriorityModal();
        }
    });
</script>
@endpush
@endsection

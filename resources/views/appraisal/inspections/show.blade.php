@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto">
    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div class="flex items-center gap-4">
            <a href="{{ route('appraisal.inspections.index') }}" class="btn btn-ghost btn-sm btn-square">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
            </a>
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Detail Inspeksi</h1>
                <p class="text-gray-600 dark:text-gray-400 mt-1">
                    {{ $inspection->project->project_code ?? '-' }} - {{ $inspection->project->name ?? '-' }}
                </p>
            </div>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('appraisal.inspections.edit', $inspection) }}" class="btn btn-outline btn-sm gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                </svg>
                Edit
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        {{-- Inspection Info --}}
        <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-6">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Informasi Inspeksi</h2>
            
            <div class="space-y-4">
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">Tanggal Inspeksi</p>
                        <p class="font-medium text-gray-900 dark:text-white">
                            {{ $inspection->inspection_date?->format('d M Y') ?? '-' }}
                        </p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">Surveyor</p>
                        <p class="font-medium text-gray-900 dark:text-white">
                            {{ $inspection->surveyor->name ?? '-' }}
                        </p>
                    </div>
                </div>

                @if($inspection->notes)
                <div>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">Catatan</p>
                    <p class="text-gray-900 dark:text-white">{{ $inspection->notes }}</p>
                </div>
                @endif
            </div>
        </div>

        {{-- Location Info --}}
        <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-6">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Lokasi</h2>
            
            <div class="space-y-4">
                <div>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">Alamat Objek</p>
                    <p class="text-gray-900 dark:text-white">
                        {{ $inspection->project->location ?? '-' }}
                    </p>
                </div>

                @if($inspection->latitude && $inspection->longitude)
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">Latitude</p>
                        <p class="font-mono text-sm text-gray-900 dark:text-white">{{ $inspection->latitude }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">Longitude</p>
                        <p class="font-mono text-sm text-gray-900 dark:text-white">{{ $inspection->longitude }}</p>
                    </div>
                </div>
                
                {{-- Map Link --}}
                <a href="https://www.google.com/maps?q={{ $inspection->latitude }},{{ $inspection->longitude }}" 
                   target="_blank" 
                   class="btn btn-outline btn-sm gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                    Lihat di Google Maps
                </a>
                @else
                <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-4 text-center">
                    <svg class="w-8 h-8 mx-auto text-gray-400 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                    </svg>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Koordinat belum diisi</p>
                </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Project Info --}}
    <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-6 mt-6">
        <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Informasi Proyek</h2>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">Kode Proyek</p>
                <a href="{{ route('appraisal.projects.show', $inspection->project) }}" 
                   class="font-medium text-blue-600 dark:text-blue-400 hover:underline">
                    {{ $inspection->project->project_code ?? '-' }}
                </a>
            </div>
            <div>
                <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">Nama Proyek</p>
                <p class="font-medium text-gray-900 dark:text-white">{{ $inspection->project->name ?? '-' }}</p>
            </div>
            <div>
                <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">Klien</p>
                <p class="font-medium text-gray-900 dark:text-white">{{ $inspection->project->client->name ?? '-' }}</p>
            </div>
        </div>
    </div>

    {{-- Documents (if any) --}}
    @if($inspection->documents && $inspection->documents->count() > 0)
    <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-6 mt-6">
        <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Dokumen Terkait</h2>
        
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            @foreach($inspection->documents as $document)
            <a href="{{ Storage::url($document->file_path) }}" target="_blank" 
               class="flex flex-col items-center p-4 rounded-lg border border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700/50">
                <svg class="w-8 h-8 text-gray-400 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                <span class="text-xs text-gray-600 dark:text-gray-400 text-center truncate w-full">
                    {{ $document->file_name }}
                </span>
            </a>
            @endforeach
        </div>
    </div>
    @endif

    {{-- Actions --}}
    <div class="flex items-center justify-between mt-6">
        <a href="{{ route('appraisal.projects.show', $inspection->project) }}" class="btn btn-ghost gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
            </svg>
            Kembali ke Proyek
        </a>

        <form action="{{ route('appraisal.inspections.destroy', $inspection) }}" method="POST" 
              onsubmit="return confirm('Yakin ingin menghapus inspeksi ini?')">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-error btn-outline btn-sm gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                </svg>
                Hapus
            </button>
        </form>
    </div>
</div>
@endsection

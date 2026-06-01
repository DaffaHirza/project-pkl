@extends('layouts.app')

@section('title', 'Edit Rekapitulasi')

@section('content')
<div class="max-w-3xl mx-auto space-y-6">
    {{-- Header --}}
    <div>
        <a href="{{ route('kanban.recapitulations.show', $recapitulation) }}" 
           class="inline-flex items-center gap-2 text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white transition mb-4 text-sm">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            Kembali ke Detail
        </a>
        <div>
            <h1 class="text-xl font-semibold text-gray-800 dark:text-white">Edit Rekapitulasi</h1>
            <p class="text-sm text-gray-500 dark:text-gray-400">Perbarui informasi rekapitulasi</p>
        </div>
    </div>

    {{-- Alert --}}
    @if(session('error'))
    <div class="p-4 rounded-lg bg-red-50 border border-red-200 dark:bg-red-900/20 dark:border-red-800 flex items-center gap-3">
        <div class="w-8 h-8 rounded bg-red-100 dark:bg-red-900/30 flex items-center justify-center">
            <svg class="w-4 h-4 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
        </div>
        <p class="text-sm text-red-700 dark:text-red-400">{{ session('error') }}</p>
    </div>
    @endif

    {{-- Form --}}
    <form action="{{ route('kanban.recapitulations.update', $recapitulation) }}" method="POST" class="space-y-6">
        @csrf
        @method('PUT')
        
        <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-6">
            {{-- Title --}}
            <div class="mb-6">
                <label for="title" class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">
                    Judul Rekapitulasi <span class="text-red-500">*</span>
                </label>
                <input type="text" name="title" id="title" value="{{ old('title', $recapitulation->title) }}"
                       class="w-full px-3 py-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white text-sm focus:ring-brand-500 focus:border-brand-500"
                       required>
                @error('title')
                <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                @enderror
            </div>

            {{-- Period --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                <div>
                    <label for="period_start" class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">
                        Tanggal Mulai <span class="text-red-500">*</span>
                    </label>
                    <input type="date" name="period_start" id="period_start" 
                           value="{{ old('period_start', $recapitulation->period_start->format('Y-m-d')) }}"
                           class="w-full px-3 py-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white text-sm focus:ring-brand-500 focus:border-brand-500"
                           required>
                    @error('period_start')
                    <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="period_end" class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">
                        Tanggal Akhir <span class="text-red-500">*</span>
                    </label>
                    <input type="date" name="period_end" id="period_end" 
                           value="{{ old('period_end', $recapitulation->period_end->format('Y-m-d')) }}"
                           class="w-full px-3 py-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white text-sm focus:ring-brand-500 focus:border-brand-500"
                           required>
                    @error('period_end')
                    <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            {{-- Summary --}}
            <div>
                <label for="summary" class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">
                    Ringkasan / Catatan
                </label>
                <textarea name="summary" id="summary" rows="4"
                          class="w-full px-3 py-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white text-sm focus:ring-brand-500 focus:border-brand-500 resize-none"
                          placeholder="Ringkasan umum atau catatan untuk periode ini...">{{ old('summary', $recapitulation->summary) }}</textarea>
                @error('summary')
                <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                @enderror
            </div>
        </div>

        {{-- Submit --}}
        <div class="flex items-center justify-end gap-3">
            <a href="{{ route('kanban.recapitulations.show', $recapitulation) }}" 
               class="px-4 py-2 text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white transition text-sm">
                Batal
            </a>
            <button type="submit" 
                    class="px-4 py-2 bg-gray-800 dark:bg-gray-600 hover:bg-gray-700 dark:hover:bg-gray-500 text-white rounded-lg font-medium text-sm transition">
                Simpan Perubahan
            </button>
        </div>
    </form>
    
    {{-- Delete Form (separate from edit form) --}}
    <div class="flex justify-start">
        <form action="{{ route('kanban.recapitulations.destroy', $recapitulation) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus rekapitulasi ini?')">
            @csrf
            @method('DELETE')
            <button type="submit" class="inline-flex items-center gap-2 px-3 py-2 text-red-600 hover:text-red-700 dark:text-red-400 dark:hover:text-red-300 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-lg transition text-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                </svg>
                Hapus Rekapitulasi
            </button>
        </form>
    </div>
</div>
@endsection

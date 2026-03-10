@extends('layouts.app')

@section('title', 'Edit Rekapitulasi')

@section('content')
<div class="max-w-3xl mx-auto">
    {{-- Header --}}
    <div class="mb-6">
        <a href="{{ route('kanban.recapitulations.show', $recapitulation) }}" class="inline-flex items-center gap-2 text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white transition mb-4">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            Kembali
        </a>
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Edit Rekapitulasi</h1>
        <p class="text-gray-600 dark:text-gray-400 mt-1">Perbarui informasi rekapitulasi</p>
    </div>

    {{-- Alert --}}
    @if(session('error'))
    <div class="mb-6 p-4 rounded-lg bg-red-50 border border-red-200 dark:bg-red-900/20 dark:border-red-800">
        <p class="text-red-700 dark:text-red-400">{{ session('error') }}</p>
    </div>
    @endif

    {{-- Form --}}
    <form action="{{ route('kanban.recapitulations.update', $recapitulation) }}" method="POST" class="space-y-6">
        @csrf
        @method('PUT')
        
        <div class="bg-white dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-800 p-6">
            {{-- Title --}}
            <div class="mb-6">
                <label for="title" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Judul Rekapitulasi <span class="text-red-500">*</span>
                </label>
                <input type="text" name="title" id="title" value="{{ old('title', $recapitulation->title) }}"
                       class="w-full px-4 py-2.5 rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-brand-500 focus:border-brand-500"
                       required>
                @error('title')
                <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                @enderror
            </div>

            {{-- Period --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                <div>
                    <label for="period_start" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Tanggal Mulai <span class="text-red-500">*</span>
                    </label>
                    <input type="date" name="period_start" id="period_start" 
                           value="{{ old('period_start', $recapitulation->period_start->format('Y-m-d')) }}"
                           class="w-full px-4 py-2.5 rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-brand-500 focus:border-brand-500"
                           required>
                    @error('period_start')
                    <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="period_end" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Tanggal Akhir <span class="text-red-500">*</span>
                    </label>
                    <input type="date" name="period_end" id="period_end" 
                           value="{{ old('period_end', $recapitulation->period_end->format('Y-m-d')) }}"
                           class="w-full px-4 py-2.5 rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-brand-500 focus:border-brand-500"
                           required>
                    @error('period_end')
                    <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            {{-- Summary --}}
            <div>
                <label for="summary" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Ringkasan / Catatan
                </label>
                <textarea name="summary" id="summary" rows="4"
                          class="w-full px-4 py-2.5 rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-brand-500 focus:border-brand-500"
                          placeholder="Ringkasan umum atau catatan untuk periode ini...">{{ old('summary', $recapitulation->summary) }}</textarea>
                @error('summary')
                <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                @enderror
            </div>
        </div>

        {{-- Submit --}}
        <div class="flex items-center justify-between">
            <form action="{{ route('kanban.recapitulations.destroy', $recapitulation) }}" method="POST" class="inline" onsubmit="return confirm('Yakin ingin menghapus rekapitulasi ini?')">
                @csrf
                @method('DELETE')
                <button type="submit" class="px-4 py-2.5 text-red-600 hover:text-red-700 dark:text-red-400 dark:hover:text-red-300 transition">
                    Hapus Rekapitulasi
                </button>
            </form>
            
            <div class="flex items-center gap-3">
                <a href="{{ route('kanban.recapitulations.show', $recapitulation) }}" class="px-4 py-2.5 text-gray-700 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white transition">
                    Batal
                </a>
                <button type="submit" class="px-6 py-2.5 bg-brand-500 hover:bg-brand-600 text-white rounded-lg font-medium transition">
                    Simpan Perubahan
                </button>
            </div>
        </div>
    </form>
</div>
@endsection

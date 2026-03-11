@extends('layouts.app')

@section('title', 'Buat Rekapitulasi')

@section('content')
<div class="max-w-3xl mx-auto space-y-6">
    {{-- Header --}}
    <div>
        <a href="{{ route('kanban.recapitulations.index') }}" 
           class="inline-flex items-center gap-2 text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white transition mb-4 text-sm">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            Kembali ke Daftar
        </a>
        <div>
            <h1 class="text-xl font-semibold text-gray-800 dark:text-white">Buat Rekapitulasi Baru</h1>
            <p class="text-sm text-gray-500 dark:text-gray-400">Buat laporan progress untuk evaluasi mingguan</p>
        </div>
    </div>

    {{-- Form --}}
    <form action="{{ route('kanban.recapitulations.store') }}" method="POST" class="space-y-6">
        @csrf
        
        <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-6">
            {{-- Title --}}
            <div class="mb-6">
                <label for="title" class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">
                    Judul Rekapitulasi <span class="text-red-500">*</span>
                </label>
                <input type="text" name="title" id="title" value="{{ old('title', $suggestedTitle) }}"
                       class="w-full px-3 py-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white text-sm focus:ring-brand-500 focus:border-brand-500"
                       placeholder="Contoh: Rekapitulasi Minggu 1 Maret 2026" required>
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
                           value="{{ old('period_start', $suggestedPeriod['start']->format('Y-m-d')) }}"
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
                           value="{{ old('period_end', $suggestedPeriod['end']->format('Y-m-d')) }}"
                           class="w-full px-3 py-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white text-sm focus:ring-brand-500 focus:border-brand-500"
                           required>
                    @error('period_end')
                    <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            {{-- Summary --}}
            <div class="mb-6">
                <label for="summary" class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">
                    Ringkasan / Catatan
                </label>
                <textarea name="summary" id="summary" rows="4"
                          class="w-full px-3 py-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white text-sm focus:ring-brand-500 focus:border-brand-500 resize-none"
                          placeholder="Ringkasan umum atau catatan untuk periode ini...">{{ old('summary') }}</textarea>
                @error('summary')
                <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                @enderror
            </div>

            {{-- Auto Generate Option --}}
            <div class="p-4 bg-gray-50 dark:bg-gray-700/50 rounded-lg border border-gray-200 dark:border-gray-600">
                <label class="flex items-start gap-3 cursor-pointer">
                    <div class="flex-shrink-0 mt-0.5">
                        <input type="checkbox" name="auto_generate" value="1" checked
                               class="w-4 h-4 text-brand-600 bg-white dark:bg-gray-800 border-gray-300 dark:border-gray-600 rounded focus:ring-brand-500">
                    </div>
                    <div>
                        <span class="font-medium text-sm text-gray-900 dark:text-white">Auto-generate dari aktivitas</span>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">
                            Secara otomatis mengisi daftar asset dan aktivitas berdasarkan catatan/notes yang ada dalam periode ini.
                        </p>
                    </div>
                </label>
            </div>
        </div>

        {{-- Submit --}}
        <div class="flex items-center justify-end gap-3">
            <a href="{{ route('kanban.recapitulations.index') }}" 
               class="px-4 py-2 text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white transition text-sm">
                Batal
            </a>
            <button type="submit" 
                    class="px-4 py-2 bg-gray-800 dark:bg-gray-600 hover:bg-gray-700 dark:hover:bg-gray-500 text-white rounded-lg font-medium text-sm transition">
                Buat Rekapitulasi
            </button>
        </div>
    </form>
</div>

@push('scripts')
<script>
// Auto-update title when dates change
document.addEventListener('DOMContentLoaded', function() {
    const startInput = document.getElementById('period_start');
    const titleInput = document.getElementById('title');
    
    startInput.addEventListener('change', function() {
        const date = new Date(this.value);
        const weekNum = Math.ceil(date.getDate() / 7);
        const months = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 
                       'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
        const month = months[date.getMonth()];
        const year = date.getFullYear();
        
        titleInput.value = `Rekapitulasi Minggu ${weekNum} ${month} ${year}`;
    });
});
</script>
@endpush
@endsection

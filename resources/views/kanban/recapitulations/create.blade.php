@extends('layouts.app')

@section('title', 'Buat Rekapitulasi')

@section('content')
<div class="max-w-3xl mx-auto">
    {{-- Header --}}
    <div class="mb-6">
        <a href="{{ route('kanban.recapitulations.index') }}" class="inline-flex items-center gap-2 text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white transition mb-4">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            Kembali
        </a>
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Buat Rekapitulasi Baru</h1>
        <p class="text-gray-600 dark:text-gray-400 mt-1">Buat laporan progress untuk evaluasi mingguan</p>
    </div>

    {{-- Form --}}
    <form action="{{ route('kanban.recapitulations.store') }}" method="POST" class="space-y-6">
        @csrf
        
        <div class="bg-white dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-800 p-6">
            {{-- Title --}}
            <div class="mb-6">
                <label for="title" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Judul Rekapitulasi <span class="text-red-500">*</span>
                </label>
                <input type="text" name="title" id="title" value="{{ old('title', $suggestedTitle) }}"
                       class="w-full px-4 py-2.5 rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-brand-500 focus:border-brand-500"
                       placeholder="Contoh: Rekapitulasi Minggu 1 Maret 2026" required>
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
                           value="{{ old('period_start', $suggestedPeriod['start']->format('Y-m-d')) }}"
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
                           value="{{ old('period_end', $suggestedPeriod['end']->format('Y-m-d')) }}"
                           class="w-full px-4 py-2.5 rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-brand-500 focus:border-brand-500"
                           required>
                    @error('period_end')
                    <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            {{-- Summary --}}
            <div class="mb-6">
                <label for="summary" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Ringkasan / Catatan
                </label>
                <textarea name="summary" id="summary" rows="4"
                          class="w-full px-4 py-2.5 rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-brand-500 focus:border-brand-500"
                          placeholder="Ringkasan umum atau catatan untuk periode ini...">{{ old('summary') }}</textarea>
                @error('summary')
                <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                @enderror
            </div>

            {{-- Auto Generate Option --}}
            <div class="p-4 bg-blue-50 dark:bg-blue-900/20 rounded-lg border border-blue-200 dark:border-blue-800">
                <label class="flex items-start gap-3 cursor-pointer">
                    <input type="checkbox" name="auto_generate" value="1" checked
                           class="mt-1 w-4 h-4 text-brand-600 bg-white border-gray-300 rounded focus:ring-brand-500">
                    <div>
                        <span class="font-medium text-gray-900 dark:text-white">Auto-generate dari aktivitas</span>
                        <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                            Secara otomatis mengisi daftar asset dan aktivitas berdasarkan catatan/notes yang ada dalam periode ini.
                            Asset yang sedang dalam proses (belum selesai) juga akan dimasukkan.
                        </p>
                    </div>
                </label>
            </div>
        </div>

        {{-- Submit --}}
        <div class="flex items-center justify-end gap-3">
            <a href="{{ route('kanban.recapitulations.index') }}" class="px-4 py-2.5 text-gray-700 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white transition">
                Batal
            </a>
            <button type="submit" class="px-6 py-2.5 bg-brand-500 hover:bg-brand-600 text-white rounded-lg font-medium transition">
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

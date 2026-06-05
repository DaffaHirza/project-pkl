@extends('layouts.app')

@section('title', 'Edit Rekapitulasi')

@section('content')
    <div class="max-w-4xl mx-auto space-y-6">
        <div>
            <a href="{{ route('kanban.recapitulations.show', $recapitulation) }}"
                class="inline-flex items-center gap-2 text-sm text-gray-500 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white mb-3">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Kembali ke detail
            </a>

            <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4">
                <div>
                    <h1 class="text-2xl font-semibold text-gray-900 dark:text-white">
                        Edit Rekapitulasi
                    </h1>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                        Perbarui periode dan ringkasan laporan.
                    </p>
                </div>

                <span
                    class="inline-flex w-fit px-3 py-1 rounded-full text-xs font-medium
                {{ $recapitulation->status === 'published'
                    ? 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400'
                    : 'bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400' }}">
                    {{ $recapitulation->status_label }}
                </span>
            </div>
        </div>

        @if (session('error'))
            <div class="p-4 rounded-xl bg-red-50 border border-red-200 dark:bg-red-900/20 dark:border-red-800">
                <p class="text-sm text-red-700 dark:text-red-400">{{ session('error') }}</p>
            </div>
        @endif

        <form action="{{ route('kanban.recapitulations.update', $recapitulation) }}" method="POST" class="space-y-6">
            @csrf
            @method('PUT')

            @include('kanban.recapitulations._form', [
                'mode' => 'edit',
                'recapitulation' => $recapitulation,
            ])

            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                <button type="button" onclick="openDeleteRecapModal()"
                    class="inline-flex items-center justify-center gap-2 px-4 py-2 text-sm font-medium rounded-xl text-red-600 hover:bg-red-50 dark:text-red-400 dark:hover:bg-red-900/20 transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>
                    Hapus Rekapitulasi
                </button>

                <div class="flex justify-end gap-3">
                    <a href="{{ route('kanban.recapitulations.show', $recapitulation) }}"
                        class="px-4 py-2 text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white">
                        Batal
                    </a>

                    <button type="submit"
                        class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-gray-900 hover:bg-gray-800 dark:bg-gray-100 dark:hover:bg-white text-white dark:text-gray-900 text-sm font-medium transition">
                        Simpan Perubahan
                    </button>
                </div>
            </div>
        </form>

        <form id="deleteRecapForm" action="{{ route('kanban.recapitulations.destroy', $recapitulation) }}" method="POST">
            @csrf
            @method('DELETE')
        </form>
    </div>

    <div id="deleteRecapModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/50 px-4">
        <div
            class="w-full max-w-md rounded-2xl bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 shadow-xl p-6">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-white">
                Hapus Rekapitulasi?
            </h2>
            <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                Rekapitulasi <span class="font-semibold">{{ $recapitulation->title }}</span> akan dihapus beserta semua item
                progress di dalamnya.
            </p>
            <p class="mt-2 text-xs text-red-600 dark:text-red-400">
                Tindakan ini tidak dapat dibatalkan.
            </p>

            <div class="mt-6 flex justify-end gap-3">
                <button type="button" onclick="closeDeleteRecapModal()"
                    class="px-4 py-2 rounded-xl border border-gray-300 dark:border-gray-700 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800">
                    Batal
                </button>
                <button type="button" onclick="document.getElementById('deleteRecapForm').submit()"
                    class="px-4 py-2 rounded-xl bg-red-600 hover:bg-red-700 text-white text-sm font-medium">
                    Ya, Hapus
                </button>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            function openDeleteRecapModal() {
                const modal = document.getElementById('deleteRecapModal');
                modal.classList.remove('hidden');
                modal.classList.add('flex');
            }

            function closeDeleteRecapModal() {
                const modal = document.getElementById('deleteRecapModal');
                modal.classList.add('hidden');
                modal.classList.remove('flex');
            }

            document.addEventListener('keydown', function(event) {
                if (event.key === 'Escape') closeDeleteRecapModal();
            });
        </script>
    @endpush
@endsection

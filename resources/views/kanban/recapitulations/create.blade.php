@extends('layouts.app')

@section('title', 'Buat Rekapitulasi')

@section('content')
    <div class="max-w-4xl mx-auto space-y-6">
        <div class="flex items-start justify-between gap-4">
            <div>
                <a href="{{ route('kanban.recapitulations.index') }}"
                    class="inline-flex items-center gap-2 text-sm text-gray-500 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white mb-3">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    Kembali ke daftar
                </a>

                <h1 class="text-2xl font-semibold text-gray-900 dark:text-white">
                    Buat Rekapitulasi Baru
                </h1>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                    Buat laporan progress mingguan untuk evaluasi pekerjaan asset.
                </p>
            </div>
        </div>

        <form action="{{ route('kanban.recapitulations.store') }}" method="POST" class="space-y-6">
            @csrf

            @include('kanban.recapitulations._form', [
                'mode' => 'create',
                'suggestedTitle' => $suggestedTitle,
                'suggestedPeriod' => $suggestedPeriod,
            ])

            <div class="flex items-center justify-end gap-3">
                <a href="{{ route('kanban.recapitulations.index') }}"
                    class="px-4 py-2 text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white">
                    Batal
                </a>

                <button type="submit"
                    class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-gray-900 hover:bg-gray-800 dark:bg-gray-100 dark:hover:bg-white text-white dark:text-gray-900 text-sm font-medium transition">
                    Buat Rekapitulasi
                </button>
            </div>
        </form>
    </div>
@endsection

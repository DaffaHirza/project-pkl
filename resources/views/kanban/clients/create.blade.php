@extends('layouts.app')

@section('title', 'Tambah Klien Baru')

@section('content')
<div>
    {{-- Header --}}
    <div class="mb-6">
        <a href="{{ route('kanban.clients.index') }}" class="inline-flex items-center text-sm text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300 mb-2">
            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
            </svg>
            Kembali
        </a>
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-lg bg-gradient-to-br from-blue-500 to-purple-500 flex items-center justify-center">
                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
            </div>
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Tambah Klien Baru</h1>
                <p class="text-gray-600 dark:text-gray-400">Pilih jenis klien yang ingin ditambahkan</p>
            </div>
        </div>
    </div>

    {{-- Client Type Selection --}}
    <div class="grid md:grid-cols-3 gap-6">
        {{-- Bank --}}
        <a href="{{ route('kanban.clients.create.bank') }}" class="group block bg-white dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-800 p-6 hover:border-blue-500 dark:hover:border-blue-500 transition">
            <div class="text-center">
                <div class="w-16 h-16 rounded-xl bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center mx-auto group-hover:bg-blue-200 dark:group-hover:bg-blue-900/50 transition">
                    <svg class="w-8 h-8 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                    </svg>
                </div>
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white mt-4 group-hover:text-blue-600 dark:group-hover:text-blue-400 transition">Bank</h2>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-2">Tambah bank baru dengan nomor SPK dan daftar debitur</p>
                <ul class="text-xs text-gray-400 dark:text-gray-500 mt-4 space-y-1 text-left max-w-[200px] mx-auto">
                    <li class="flex items-center gap-2">
                        <svg class="w-3 h-3 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        Nama bank
                    </li>
                    <li class="flex items-center gap-2">
                        <svg class="w-3 h-3 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        Nomor SPK
                    </li>
                    <li class="flex items-center gap-2">
                        <svg class="w-3 h-3 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        Multiple debitur
                    </li>
                </ul>
            </div>
        </a>

        {{-- PT/CV Induk --}}
        <a href="{{ route('kanban.clients.create.perusahaan-induk') }}" class="group block bg-white dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-800 p-6 hover:border-purple-500 dark:hover:border-purple-500 transition">
            <div class="text-center">
                <div class="w-16 h-16 rounded-xl bg-purple-100 dark:bg-purple-900/30 flex items-center justify-center mx-auto group-hover:bg-purple-200 dark:group-hover:bg-purple-900/50 transition">
                    <svg class="w-8 h-8 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 21v-4m0 0V5a2 2 0 012-2h6.5l1 1H21l-3 6 3 6h-8.5l-1-1H5a2 2 0 00-2 2zm9-13.5V9" />
                    </svg>
                </div>
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white mt-4 group-hover:text-purple-600 dark:group-hover:text-purple-400 transition">PT/CV Induk</h2>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-2">Tambah perusahaan induk dengan anak perusahaan</p>
                <ul class="text-xs text-gray-400 dark:text-gray-500 mt-4 space-y-1 text-left max-w-[200px] mx-auto">
                    <li class="flex items-center gap-2">
                        <svg class="w-3 h-3 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        Nama perusahaan
                    </li>
                    <li class="flex items-center gap-2">
                        <svg class="w-3 h-3 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        Nomor SPK
                    </li>
                    <li class="flex items-center gap-2">
                        <svg class="w-3 h-3 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        Multiple anak PT/CV
                    </li>
                </ul>
            </div>
        </a>

        {{-- PT/CV / Debitur --}}
        <a href="{{ route('kanban.clients.create.klien') }}" class="group block bg-white dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-800 p-6 hover:border-orange-500 dark:hover:border-orange-500 transition">
            <div class="text-center">
                <div class="w-16 h-16 rounded-xl bg-orange-100 dark:bg-orange-900/30 flex items-center justify-center mx-auto group-hover:bg-orange-200 dark:group-hover:bg-orange-900/50 transition">
                    <svg class="w-8 h-8 text-orange-600 dark:text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                </div>
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white mt-4 group-hover:text-orange-600 dark:group-hover:text-orange-400 transition">Debitur / PT/CV</h2>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-2">Tambah debitur atau anak perusahaan secara individual</p>
                <ul class="text-xs text-gray-400 dark:text-gray-500 mt-4 space-y-1 text-left max-w-[200px] mx-auto">
                    <li class="flex items-center gap-2">
                        <svg class="w-3 h-3 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        Nama klien
                    </li>
                    <li class="flex items-center gap-2">
                        <svg class="w-3 h-3 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        Tipe (Debitur/PT/CV)
                    </li>
                    <li class="flex items-center gap-2">
                        <svg class="w-3 h-3 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        Pilih induk
                    </li>
                </ul>
            </div>
        </a>
    </div>
</div>
@endsection

@extends('layouts.app')

@section('title', 'Klien')

@section('content')
<div class="max-w-5xl mx-auto">
    {{-- Header --}}
    <div class="mb-8">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Klien</h1>
        <p class="text-gray-500 dark:text-gray-400 mt-1">Kelola data perusahaan dan debitur</p>
    </div>

    {{-- Alert --}}
    @if(session('success'))
    <div class="mb-6 px-4 py-3 rounded-lg bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800">
        <p class="text-sm text-green-700 dark:text-green-400">{{ session('success') }}</p>
    </div>
    @endif

    {{-- Stats Overview --}}
    <div class="grid grid-cols-4 gap-3 mb-8">
        <div class="bg-white dark:bg-gray-900 rounded-xl p-4 border border-gray-100 dark:border-gray-800">
            <p class="text-3xl font-bold text-gray-900 dark:text-white">{{ $stats['bank'] + $stats['pt_cv_induk'] + $stats['debitur'] + $stats['pt_cv_anak'] }}</p>
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Total</p>
        </div>
        <div class="bg-white dark:bg-gray-900 rounded-xl p-4 border border-gray-100 dark:border-gray-800">
            <p class="text-3xl font-bold text-blue-600 dark:text-blue-400">{{ $stats['bank'] }}</p>
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Bank</p>
        </div>
        <div class="bg-white dark:bg-gray-900 rounded-xl p-4 border border-gray-100 dark:border-gray-800">
            <p class="text-3xl font-bold text-purple-600 dark:text-purple-400">{{ $stats['pt_cv_induk'] + $stats['pt_cv_anak'] }}</p>
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">PT/CV</p>
        </div>
        <div class="bg-white dark:bg-gray-900 rounded-xl p-4 border border-gray-100 dark:border-gray-800">
            <p class="text-3xl font-bold text-orange-600 dark:text-orange-400">{{ $stats['debitur'] }}</p>
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Debitur</p>
        </div>
    </div>

    {{-- Main Navigation Cards --}}
    <div class="grid md:grid-cols-2 gap-4 mb-8">
        {{-- Perusahaan --}}
        <a href="{{ route('kanban.clients.perusahaan') }}" class="group relative bg-gradient-to-br from-blue-500 to-blue-600 rounded-2xl p-6 text-white overflow-hidden hover:shadow-lg hover:shadow-blue-500/25 transition-all duration-300">
            <div class="absolute top-0 right-0 w-32 h-32 bg-white/10 rounded-full -translate-y-1/2 translate-x-1/2"></div>
            <div class="relative">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-12 h-12 rounded-xl bg-white/20 backdrop-blur flex items-center justify-center">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                        </svg>
                    </div>
                    <svg class="w-5 h-5 opacity-50 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </div>
                <h2 class="text-xl font-semibold">Perusahaan</h2>
                <p class="text-blue-100 text-sm mt-1">Bank & PT/CV Induk</p>
                <div class="flex gap-4 mt-4 pt-4 border-t border-white/20">
                    <div>
                        <p class="text-2xl font-bold">{{ $stats['bank'] }}</p>
                        <p class="text-xs text-blue-200">Bank</p>
                    </div>
                    <div>
                        <p class="text-2xl font-bold">{{ $stats['pt_cv_induk'] }}</p>
                        <p class="text-xs text-blue-200">PT/CV</p>
                    </div>
                </div>
            </div>
        </a>

        {{-- Debitur --}}
        <a href="{{ route('kanban.clients.debitur') }}" class="group relative bg-gradient-to-br from-orange-500 to-orange-600 rounded-2xl p-6 text-white overflow-hidden hover:shadow-lg hover:shadow-orange-500/25 transition-all duration-300">
            <div class="absolute top-0 right-0 w-32 h-32 bg-white/10 rounded-full -translate-y-1/2 translate-x-1/2"></div>
            <div class="relative">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-12 h-12 rounded-xl bg-white/20 backdrop-blur flex items-center justify-center">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                    </div>
                    <svg class="w-5 h-5 opacity-50 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </div>
                <h2 class="text-xl font-semibold">Debitur</h2>
                <p class="text-orange-100 text-sm mt-1">Debitur & Anak Perusahaan</p>
                <div class="flex gap-4 mt-4 pt-4 border-t border-white/20">
                    <div>
                        <p class="text-2xl font-bold">{{ $stats['debitur'] }}</p>
                        <p class="text-xs text-orange-200">Debitur</p>
                    </div>
                    <div>
                        <p class="text-2xl font-bold">{{ $stats['pt_cv_anak'] }}</p>
                        <p class="text-xs text-orange-200">PT/CV Anak</p>
                    </div>
                </div>
            </div>
        </a>
    </div>

    {{-- Quick Add --}}
    <div class="bg-white dark:bg-gray-900 rounded-2xl border border-gray-100 dark:border-gray-800 p-6">
        <h3 class="text-sm font-medium text-gray-900 dark:text-white mb-4">Tambah Cepat</h3>
        <div class="grid grid-cols-3 gap-3">
            <a href="{{ route('kanban.clients.create.bank') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl border border-gray-200 dark:border-gray-700 hover:border-blue-300 dark:hover:border-blue-700 hover:bg-blue-50 dark:hover:bg-blue-900/10 transition group">
                <div class="w-10 h-10 rounded-lg bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center flex-shrink-0 group-hover:bg-blue-200 dark:group-hover:bg-blue-900/50 transition">
                    <svg class="w-5 h-5 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                </div>
                <div>
                    <p class="font-medium text-gray-900 dark:text-white text-sm">Bank</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400">+ debitur</p>
                </div>
            </a>
            <a href="{{ route('kanban.clients.create.perusahaan-induk') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl border border-gray-200 dark:border-gray-700 hover:border-purple-300 dark:hover:border-purple-700 hover:bg-purple-50 dark:hover:bg-purple-900/10 transition group">
                <div class="w-10 h-10 rounded-lg bg-purple-100 dark:bg-purple-900/30 flex items-center justify-center flex-shrink-0 group-hover:bg-purple-200 dark:group-hover:bg-purple-900/50 transition">
                    <svg class="w-5 h-5 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                </div>
                <div>
                    <p class="font-medium text-gray-900 dark:text-white text-sm">PT/CV</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400">+ anak</p>
                </div>
            </a>
            <a href="{{ route('kanban.clients.create.klien') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl border border-gray-200 dark:border-gray-700 hover:border-orange-300 dark:hover:border-orange-700 hover:bg-orange-50 dark:hover:bg-orange-900/10 transition group">
                <div class="w-10 h-10 rounded-lg bg-orange-100 dark:bg-orange-900/30 flex items-center justify-center flex-shrink-0 group-hover:bg-orange-200 dark:group-hover:bg-orange-900/50 transition">
                    <svg class="w-5 h-5 text-orange-600 dark:text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                </div>
                <div>
                    <p class="font-medium text-gray-900 dark:text-white text-sm">Klien</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400">individual</p>
                </div>
            </a>
        </div>
    </div>
</div>
@endsection

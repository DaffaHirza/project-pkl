@extends('layouts.app')

@section('title', $recapitulation->title)

@section('content')
<div class="space-y-6">
    {{-- Header --}}
    <div class="flex flex-col lg:flex-row lg:items-start lg:justify-between gap-4">
        <div>
            <a href="{{ route('kanban.recapitulations.index') }}" 
               class="inline-flex items-center gap-2 text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white transition mb-4 text-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Kembali ke Daftar
            </a>
            <div>
                <div class="flex items-center gap-2 mb-1">
                    <h1 class="text-xl font-semibold text-gray-800 dark:text-white">{{ $recapitulation->title }}</h1>
                    @if($recapitulation->status === 'published')
                    <span class="px-2 py-0.5 text-xs bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400 rounded">Dipublikasikan</span>
                    @else
                    <span class="px-2 py-0.5 text-xs bg-amber-100 dark:bg-amber-900/30 text-amber-700 dark:text-amber-400 rounded">Draft</span>
                    @endif
                </div>
                <p class="text-sm text-gray-500 dark:text-gray-400">
                    <span class="inline-flex items-center gap-1">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                        {{ $recapitulation->period_label }}
                    </span>
                    <span class="mx-2">•</span>
                    <span>Dibuat oleh {{ $recapitulation->creator->name ?? 'Unknown' }}</span>
                </p>
            </div>
        </div>
        
        {{-- Actions --}}
        <div class="flex items-center gap-2 flex-wrap">
            <a href="{{ route('kanban.recapitulations.print', $recapitulation) }}" target="_blank" 
               class="inline-flex items-center gap-2 px-3 py-2 text-gray-600 dark:text-gray-300 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition text-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                </svg>
                Cetak
            </a>
            
            @if(!$recapitulation->isPublished())
            <form action="{{ route('kanban.recapitulations.regenerate', $recapitulation) }}" method="POST" class="inline">
                @csrf
                <button type="submit" class="inline-flex items-center gap-2 px-3 py-2 text-gray-600 dark:text-gray-300 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition text-sm" onclick="return confirm('Regenerate akan menghapus semua item dan generate ulang dari aktivitas. Lanjutkan?')">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                    </svg>
                    Regenerate
                </button>
            </form>
            
            <a href="{{ route('kanban.recapitulations.edit', $recapitulation) }}" 
               class="inline-flex items-center gap-2 px-3 py-2 text-gray-600 dark:text-gray-300 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition text-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                </svg>
                Edit
            </a>
            
            <form action="{{ route('kanban.recapitulations.publish', $recapitulation) }}" method="POST" class="inline">
                @csrf
                <button type="submit" class="inline-flex items-center gap-2 px-3 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg transition text-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    Publikasikan
                </button>
            </form>
            @else
            <form action="{{ route('kanban.recapitulations.unpublish', $recapitulation) }}" method="POST" class="inline">
                @csrf
                <button type="submit" class="inline-flex items-center gap-2 px-3 py-2 text-amber-700 dark:text-amber-400 bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-700 rounded-lg hover:bg-amber-100 dark:hover:bg-amber-900/30 transition text-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    Kembalikan ke Draft
                </button>
            </form>
            @endif
        </div>
    </div>

    {{-- Alert Messages --}}
    @if(session('success'))
    <div class="p-4 rounded-lg bg-green-50 border border-green-200 dark:bg-green-900/20 dark:border-green-800 flex items-center gap-3">
        <div class="w-8 h-8 rounded bg-green-100 dark:bg-green-900/30 flex items-center justify-center">
            <svg class="w-4 h-4 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
            </svg>
        </div>
        <p class="text-sm text-green-700 dark:text-green-400">{{ session('success') }}</p>
    </div>
    @endif

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

    {{-- Summary Section --}}
    @if($recapitulation->summary)
    <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-5">
        <div class="flex items-center gap-2 mb-2">
            <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h7"/>
            </svg>
            <h2 class="font-medium text-gray-900 dark:text-white">Ringkasan</h2>
        </div>
        <p class="text-sm text-gray-600 dark:text-gray-400 whitespace-pre-line">{{ $recapitulation->summary }}</p>
    </div>
    @endif

    {{-- Statistics Cards --}}
    <div class="grid grid-cols-2 md:grid-cols-5 gap-3">
        <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-4 text-center">
            <div class="w-8 h-8 rounded bg-gray-100 dark:bg-gray-700 flex items-center justify-center mx-auto mb-2">
                <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                </svg>
            </div>
            <p class="text-2xl font-semibold text-gray-900 dark:text-white">{{ $summary['total'] }}</p>
            <p class="text-xs text-gray-500 dark:text-gray-400">Total Asset</p>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-4 text-center">
            <div class="w-8 h-8 rounded bg-green-100 dark:bg-green-900/30 flex items-center justify-center mx-auto mb-2">
                <svg class="w-4 h-4 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
            </div>
            <p class="text-2xl font-semibold text-green-700 dark:text-green-400">{{ $summary['completed'] }}</p>
            <p class="text-xs text-gray-500 dark:text-gray-400">Selesai</p>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-4 text-center">
            <div class="w-8 h-8 rounded bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center mx-auto mb-2">
                <svg class="w-4 h-4 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <p class="text-2xl font-semibold text-blue-700 dark:text-blue-400">{{ $summary['in_progress'] }}</p>
            <p class="text-xs text-gray-500 dark:text-gray-400">Dikerjakan</p>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-4 text-center">
            <div class="w-8 h-8 rounded bg-amber-100 dark:bg-amber-900/30 flex items-center justify-center mx-auto mb-2">
                <svg class="w-4 h-4 text-amber-600 dark:text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                </svg>
            </div>
            <p class="text-2xl font-semibold text-amber-700 dark:text-amber-400">{{ $summary['pending_review'] }}</p>
            <p class="text-xs text-gray-500 dark:text-gray-400">Review</p>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-4 text-center">
            <div class="w-8 h-8 rounded bg-red-100 dark:bg-red-900/30 flex items-center justify-center mx-auto mb-2">
                <svg class="w-4 h-4 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                </svg>
            </div>
            <p class="text-2xl font-semibold text-red-700 dark:text-red-400">{{ $summary['blocked'] }}</p>
            <p class="text-xs text-gray-500 dark:text-gray-400">Terhambat</p>
        </div>
    </div>

    {{-- Items List --}}
    <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700">
        <div class="p-4 border-b border-gray-200 dark:border-gray-700 flex items-center justify-between">
            <h2 class="font-medium text-gray-900 dark:text-white">Daftar Asset & Progress</h2>
            @if(!$recapitulation->isPublished())
            <button type="button" onclick="openAddAssetModal()" class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs text-brand-600 dark:text-brand-400 hover:bg-brand-50 dark:hover:bg-brand-900/20 rounded transition">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Tambah Asset
            </button>
            @endif
        </div>

        @if($recapitulation->items->isEmpty())
        <div class="p-10 text-center">
            <svg class="w-10 h-10 mx-auto text-gray-300 dark:text-gray-600 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
            </svg>
            <p class="text-sm text-gray-500 dark:text-gray-400">Belum ada asset dalam rekapitulasi ini</p>
            @if(!$recapitulation->isPublished())
            <button type="button" onclick="openAddAssetModal()" class="mt-3 inline-flex items-center gap-1.5 px-3 py-1.5 bg-brand-500 hover:bg-brand-600 text-white rounded-lg text-xs transition">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Tambah Asset
            </button>
            @endif
        </div>
        @else
        <div class="divide-y divide-gray-200 dark:divide-gray-700">
            @foreach($recapitulation->items as $item)
            <div class="p-5 hover:bg-gray-50 dark:hover:bg-gray-800/50 transition" id="item-{{ $item->id }}">
                <div class="flex flex-col lg:flex-row gap-4">
                    {{-- Asset Info --}}
                    <div class="flex-1 min-w-0">
                        <div class="flex items-start gap-3 mb-3">
                            <div class="flex-1">
                                <a href="{{ route('kanban.assets.show', $item->asset) }}" class="font-medium text-gray-900 dark:text-white hover:text-brand-600 dark:hover:text-brand-400 transition">
                                    {{ $item->asset->name }}
                                </a>
                                <p class="text-sm text-gray-500 dark:text-gray-400">
                                    {{ $item->asset->client->display_name ?? 'No Client' }} • {{ ucfirst(str_replace('_', ' ', $item->asset->asset_type)) }}
                                </p>
                            </div>
                            <span class="flex-shrink-0 inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium
                                @if($item->work_status === 'completed') bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400
                                @elseif($item->work_status === 'in_progress') bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400
                                @elseif($item->work_status === 'pending_review') bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-400
                                @elseif($item->work_status === 'blocked') bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400
                                @else bg-gray-100 text-gray-700 dark:bg-gray-800 dark:text-gray-400
                                @endif">
                                {{ $item->work_status_label }}
                            </span>
                        </div>

                        {{-- Stage Progress --}}
                        <div class="mb-3">
                            <div class="flex items-center gap-2 text-sm mb-1">
                                <span class="text-gray-500 dark:text-gray-400">Progress Stage:</span>
                                <span class="font-medium text-gray-900 dark:text-white">
                                    {{ $item->stage_start }} → {{ $item->stage_end }}
                                    @if($item->has_progress)
                                    <span class="text-green-600 dark:text-green-400">(+{{ $item->stage_progress }})</span>
                                    @endif
                                </span>
                            </div>
                            <div class="flex items-center gap-2">
                                <div class="flex-1 bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                                    <div class="bg-brand-500 h-2 rounded-full transition-all" style="width: {{ $item->progress_percentage }}%"></div>
                                </div>
                                <span class="text-xs text-gray-500 dark:text-gray-400">{{ $item->progress_percentage }}%</span>
                            </div>
                        </div>

                        {{-- Activities --}}
                        @if($item->activities)
                        <div class="mb-3">
                            <p class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Aktivitas:</p>
                            <div class="text-sm text-gray-600 dark:text-gray-400 bg-gray-50 dark:bg-gray-800 rounded-lg p-3 whitespace-pre-line">{{ $item->activities }}</div>
                        </div>
                        @endif

                        {{-- Notes --}}
                        @if($item->notes)
                        <div class="mb-3">
                            <p class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Catatan:</p>
                            <p class="text-sm text-gray-600 dark:text-gray-400">{{ $item->notes }}</p>
                        </div>
                        @endif

                        {{-- Next Actions --}}
                        @if($item->next_actions)
                        <div>
                            <p class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Rencana Selanjutnya:</p>
                            <p class="text-sm text-gray-600 dark:text-gray-400">{{ $item->next_actions }}</p>
                        </div>
                        @endif
                    </div>

                    {{-- Actions --}}
                    @if(!$recapitulation->isPublished())
                    <div class="flex lg:flex-col items-center gap-2">
                        <button type="button" onclick="editItem({{ $item->id }})" class="p-2 text-gray-500 hover:text-brand-600 dark:text-gray-400 dark:hover:text-brand-400 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-800 transition" title="Edit">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                            </svg>
                        </button>
                        <button type="button" onclick="removeItem({{ $item->id }})" class="p-2 text-gray-500 hover:text-red-600 dark:text-gray-400 dark:hover:text-red-400 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-800 transition" title="Hapus">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                            </svg>
                        </button>
                    </div>
                    @endif
                </div>
            </div>
            @endforeach
        </div>
        @endif
    </div>
</div>

{{-- Add Asset Modal --}}
@if(!$recapitulation->isPublished())
<div id="addAssetModal" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-modal="true">
    <div class="flex items-center justify-center min-h-screen px-4">
        <div class="fixed inset-0 bg-black/50 transition-opacity" onclick="closeAddAssetModal()"></div>
        <div class="relative bg-white dark:bg-gray-900 rounded-xl shadow-xl max-w-lg w-full p-6 z-10">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Tambah Asset</h3>
            <div id="assetList" class="max-h-96 overflow-y-auto space-y-2">
                <p class="text-gray-500 dark:text-gray-400 text-center py-4">Memuat daftar asset...</p>
            </div>
            <div class="mt-4 flex justify-end">
                <button type="button" onclick="closeAddAssetModal()" class="px-4 py-2 text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white transition">
                    Tutup
                </button>
            </div>
        </div>
    </div>
</div>

{{-- Edit Item Modal --}}
<div id="editItemModal" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-modal="true">
    <div class="flex items-center justify-center min-h-screen px-4">
        <div class="fixed inset-0 bg-black/50 transition-opacity" onclick="closeEditItemModal()"></div>
        <div class="relative bg-white dark:bg-gray-900 rounded-xl shadow-xl max-w-lg w-full p-6 z-10">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Edit Item</h3>
            <form id="editItemForm" class="space-y-4">
                <input type="hidden" id="editItemId">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Status Pekerjaan</label>
                    <select id="editWorkStatus" class="w-full px-4 py-2 rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-900 dark:text-white">
                        <option value="not_started">Belum Dikerjakan</option>
                        <option value="in_progress">Sedang Dikerjakan</option>
                        <option value="completed">Selesai</option>
                        <option value="blocked">Terhambat</option>
                        <option value="pending_review">Menunggu Review</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Aktivitas</label>
                    <textarea id="editActivities" rows="3" class="w-full px-4 py-2 rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-900 dark:text-white" placeholder="Apa yang sudah dikerjakan..."></textarea>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Catatan/Kendala</label>
                    <textarea id="editNotes" rows="2" class="w-full px-4 py-2 rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-900 dark:text-white" placeholder="Catatan atau kendala..."></textarea>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Rencana Selanjutnya</label>
                    <textarea id="editNextActions" rows="2" class="w-full px-4 py-2 rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-900 dark:text-white" placeholder="Yang akan dikerjakan..."></textarea>
                </div>
                <div class="flex justify-end gap-3">
                    <button type="button" onclick="closeEditItemModal()" class="px-4 py-2 text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white transition">
                        Batal
                    </button>
                    <button type="submit" class="px-4 py-2 bg-brand-500 hover:bg-brand-600 text-white rounded-lg transition">
                        Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif

@push('scripts')
<script>
const recapId = {{ $recapitulation->id }};
const csrfToken = '{{ csrf_token() }}';

// Add Asset Modal
function openAddAssetModal() {
    document.getElementById('addAssetModal').classList.remove('hidden');
    loadAvailableAssets();
}

function closeAddAssetModal() {
    document.getElementById('addAssetModal').classList.add('hidden');
}

async function loadAvailableAssets() {
    const container = document.getElementById('assetList');
    container.innerHTML = '<p class="text-gray-500 text-center py-4">Memuat...</p>';
    
    try {
        const response = await fetch(`/kanban/recapitulations/${recapId}/available-assets`);
        const assets = await response.json();
        
        if (assets.length === 0) {
            container.innerHTML = '<p class="text-gray-500 text-center py-4">Semua asset sudah ditambahkan</p>';
            return;
        }
        
        container.innerHTML = assets.map(asset => `
            <button type="button" onclick="addAsset(${asset.id})" class="w-full text-left p-3 rounded-lg border border-gray-200 dark:border-gray-700 hover:border-brand-300 dark:hover:border-brand-600 hover:bg-gray-50 dark:hover:bg-gray-800 transition">
                <p class="font-medium text-gray-900 dark:text-white">${asset.name}</p>
                <p class="text-sm text-gray-500">${asset.client?.display_name || ''} • Stage ${asset.current_stage}/13</p>
            </button>
        `).join('');
    } catch (error) {
        container.innerHTML = '<p class="text-red-500 text-center py-4">Gagal memuat daftar</p>';
    }
}

async function addAsset(assetId) {
    try {
        const response = await fetch(`/kanban/recapitulations/${recapId}/items`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            },
            body: JSON.stringify({ asset_id: assetId })
        });
        
        const data = await response.json();
        if (data.success) {
            location.reload();
        } else {
            alert(data.message || 'Gagal menambahkan asset');
        }
    } catch (error) {
        alert('Terjadi kesalahan');
    }
}

// Edit Item Modal
function editItem(itemId) {
    document.getElementById('editItemId').value = itemId;
    document.getElementById('editItemModal').classList.remove('hidden');
    
    // Get current item data from DOM
    const itemEl = document.getElementById(`item-${itemId}`);
    // Pre-populate form if needed (simplified for now)
}

function closeEditItemModal() {
    document.getElementById('editItemModal').classList.add('hidden');
}

document.getElementById('editItemForm')?.addEventListener('submit', async function(e) {
    e.preventDefault();
    
    const itemId = document.getElementById('editItemId').value;
    const data = {
        work_status: document.getElementById('editWorkStatus').value,
        activities: document.getElementById('editActivities').value,
        notes: document.getElementById('editNotes').value,
        next_actions: document.getElementById('editNextActions').value
    };
    
    try {
        const response = await fetch(`/kanban/recapitulations/items/${itemId}`, {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            },
            body: JSON.stringify(data)
        });
        
        const result = await response.json();
        if (result.success) {
            location.reload();
        } else {
            alert(result.message || 'Gagal menyimpan');
        }
    } catch (error) {
        alert('Terjadi kesalahan');
    }
});

async function removeItem(itemId) {
    if (!confirm('Hapus item ini dari rekapitulasi?')) return;
    
    try {
        const response = await fetch(`/kanban/recapitulations/items/${itemId}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': csrfToken
            }
        });
        
        const data = await response.json();
        if (data.success) {
            document.getElementById(`item-${itemId}`).remove();
        } else {
            alert(data.message || 'Gagal menghapus');
        }
    } catch (error) {
        alert('Terjadi kesalahan');
    }
}
</script>
@endpush
@endsection

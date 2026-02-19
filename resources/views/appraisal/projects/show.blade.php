@extends('layouts.app')

@php
use App\Models\ProjectKanban;
use App\Models\ProjectAsset;
@endphp

@section('content')
<div x-data="{ 
    activeTab: 'overview',
    showDeleteModal: false,
    showStatusModal: false
}">
    {{-- Header --}}
    <div class="mb-6">
        <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4">
            <div>
                {{-- Breadcrumb --}}
                <nav class="flex items-center gap-2 text-sm text-gray-500 dark:text-gray-400 mb-2">
                    <a href="{{ route('appraisal.dashboard') }}" class="hover:text-gray-700 dark:hover:text-gray-300">Appraisal</a>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                    <a href="{{ route('appraisal.projects.index') }}" class="hover:text-gray-700 dark:hover:text-gray-300">Proyek</a>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                    <span class="text-gray-700 dark:text-gray-300">{{ $project->project_code }}</span>
                </nav>

                {{-- Title --}}
                <div class="flex items-center gap-3">
                    <h1 class="text-2xl font-bold text-gray-900 dark:text-white">{{ $project->name }}</h1>
                    @php
                        $statusColors = [
                            'ongoing' => 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400',
                            'completed' => 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400',
                            'on_hold' => 'bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-400',
                            'cancelled' => 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400',
                        ];
                        $statusLabels = ProjectKanban::STATUS;
                    @endphp
                    <span class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-medium {{ $statusColors[$project->status] ?? $statusColors['ongoing'] }}">
                        {{ $statusLabels[$project->status] ?? $project->status }}
                    </span>
                </div>
                <p class="text-gray-600 dark:text-gray-400 mt-1">{{ $project->project_code }}</p>
            </div>

            {{-- Actions --}}
            <div class="flex items-center gap-2">
                <button @click="showStatusModal = true" class="btn btn-outline btn-sm gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    Ubah Status
                </button>
                <a href="{{ route('appraisal.projects.edit', $project) }}" class="btn btn-outline btn-sm gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                    </svg>
                    Edit
                </a>
                <div class="dropdown dropdown-end">
                    <label tabindex="0" class="btn btn-outline btn-sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z" />
                        </svg>
                    </label>
                    <ul tabindex="0" class="dropdown-content z-[1] menu p-2 shadow bg-base-100 rounded-box w-52">
                        <li><a @click="showDeleteModal = true" class="text-error">Hapus</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    {{-- Asset Stage Summary (Kanban Overview) --}}
    <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-4 mb-6">
        <h3 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">Progress Objek Penilaian</h3>
        <div class="flex items-center justify-between gap-2">
            @php
                $stages = ProjectAsset::STAGES;
                $assetsByStage = $project->assetsByStage;
                $totalAssets = $project->assets->count();
            @endphp
            
            @foreach($stages as $stageKey => $stageLabel)
                @php
                    $count = isset($assetsByStage[$stageKey]) ? $assetsByStage[$stageKey]->count() : 0;
                    $hasAssets = $count > 0;
                @endphp
                <div class="flex-1 text-center">
                    <div class="w-10 h-10 mx-auto rounded-full flex items-center justify-center text-sm font-medium
                        {{ $hasAssets ? 'bg-blue-500 text-white' : 'bg-gray-200 dark:bg-gray-700 text-gray-500 dark:text-gray-400' }}">
                        {{ $count }}
                    </div>
                    <span class="text-xs text-gray-500 dark:text-gray-400 mt-1 block">{{ $stageLabel }}</span>
                </div>
                @if(!$loop->last)
                    <div class="h-0.5 w-4 bg-gray-200 dark:bg-gray-700"></div>
                @endif
            @endforeach
        </div>
        @if($totalAssets > 0)
            @php
                $doneCount = isset($assetsByStage['arsip']) ? $assetsByStage['arsip']->count() : 0;
                $progressPercent = round(($doneCount / $totalAssets) * 100);
            @endphp
            <div class="mt-4">
                <div class="flex justify-between text-xs text-gray-500 dark:text-gray-400 mb-1">
                    <span>{{ $doneCount }} dari {{ $totalAssets }} objek selesai</span>
                    <span>{{ $progressPercent }}%</span>
                </div>
                <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                    <div class="bg-green-500 h-2 rounded-full" style="width: {{ $progressPercent }}%"></div>
                </div>
            </div>
        @endif
    </div>

    {{-- Tabs --}}
    <div class="border-b border-gray-200 dark:border-gray-700 mb-6">
        <nav class="-mb-px flex space-x-6 overflow-x-auto">
            <button @click="activeTab = 'overview'" 
                    :class="{ 'border-blue-500 text-blue-600 dark:text-blue-400': activeTab === 'overview' }"
                    class="py-3 px-1 border-b-2 border-transparent text-sm font-medium text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300 whitespace-nowrap">
                Overview
            </button>
            <button @click="activeTab = 'assets'" 
                    :class="{ 'border-blue-500 text-blue-600 dark:text-blue-400': activeTab === 'assets' }"
                    class="py-3 px-1 border-b-2 border-transparent text-sm font-medium text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300 whitespace-nowrap">
                Objek Penilaian ({{ $totalAssets }})
            </button>
            <button @click="activeTab = 'invoices'" 
                    :class="{ 'border-blue-500 text-blue-600 dark:text-blue-400': activeTab === 'invoices' }"
                    class="py-3 px-1 border-b-2 border-transparent text-sm font-medium text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300 whitespace-nowrap">
                Invoice
            </button>
            <button @click="activeTab = 'activities'" 
                    :class="{ 'border-blue-500 text-blue-600 dark:text-blue-400': activeTab === 'activities' }"
                    class="py-3 px-1 border-b-2 border-transparent text-sm font-medium text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300 whitespace-nowrap">
                Aktivitas
            </button>
        </nav>
    </div>

    {{-- Tab Content --}}
    <div>
        {{-- Overview Tab --}}
        <div x-show="activeTab === 'overview'" x-cloak>
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                {{-- Main Info --}}
                <div class="lg:col-span-2 space-y-6">
                    {{-- Project Details --}}
                    <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-6">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Detail Proyek</h3>
                        <dl class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <dt class="text-sm text-gray-500 dark:text-gray-400">Kode Proyek</dt>
                                <dd class="text-sm font-medium text-gray-900 dark:text-white font-mono">{{ $project->project_code }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm text-gray-500 dark:text-gray-400">Nama Proyek</dt>
                                <dd class="text-sm font-medium text-gray-900 dark:text-white">{{ $project->name }}</dd>
                            </div>
                            <div class="sm:col-span-2">
                                <dt class="text-sm text-gray-500 dark:text-gray-400">Deskripsi</dt>
                                <dd class="text-sm font-medium text-gray-900 dark:text-white">{{ $project->description ?? '-' }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm text-gray-500 dark:text-gray-400">Target Selesai</dt>
                                <dd class="text-sm font-medium text-gray-900 dark:text-white">
                                    @if($project->due_date)
                                        {{ $project->due_date->format('d M Y') }}
                                        @if($project->due_date->isPast() && $project->status !== 'completed')
                                            <span class="text-red-500 text-xs">(Lewat)</span>
                                        @elseif($project->due_date->diffInDays(now()) <= 7 && $project->status !== 'completed')
                                            <span class="text-yellow-500 text-xs">({{ $project->due_date->diffForHumans() }})</span>
                                        @endif
                                    @else
                                        -
                                    @endif
                                </dd>
                            </div>
                            <div>
                                <dt class="text-sm text-gray-500 dark:text-gray-400">Status</dt>
                                <dd class="text-sm">
                                    <span class="badge {{ str_replace(['bg-', 'text-'], ['badge-', ''], $statusColors[$project->status] ?? '') }}">
                                        {{ $statusLabels[$project->status] ?? $project->status }}
                                    </span>
                                </dd>
                            </div>
                            <div>
                                <dt class="text-sm text-gray-500 dark:text-gray-400">Total Objek</dt>
                                <dd class="text-sm font-medium text-gray-900 dark:text-white">{{ $project->total_assets }} objek</dd>
                            </div>
                            <div>
                                <dt class="text-sm text-gray-500 dark:text-gray-400">Dibuat</dt>
                                <dd class="text-sm font-medium text-gray-900 dark:text-white">{{ $project->created_at->format('d M Y H:i') }}</dd>
                            </div>
                        </dl>
                    </div>

                    {{-- Proposal & Contract Info --}}
                    <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-6">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Proposal & Kontrak</h3>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            {{-- Proposal --}}
                            <div class="p-4 bg-gray-50 dark:bg-gray-900/50 rounded-lg">
                                <h4 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Proposal</h4>
                                @if($project->latestProposal)
                                    <p class="text-sm text-gray-600 dark:text-gray-400">
                                        {{ $project->latestProposal->proposal_number }}
                                    </p>
                                    <p class="text-xs text-gray-500 dark:text-gray-500 mt-1">
                                        Status: 
                                        <span class="font-medium">
                                            {{ ucfirst($project->latestProposal->status) }}
                                        </span>
                                    </p>
                                @else
                                    <p class="text-sm text-gray-400 dark:text-gray-500 italic">Belum ada proposal</p>
                                @endif
                            </div>
                            {{-- Contract --}}
                            <div class="p-4 bg-gray-50 dark:bg-gray-900/50 rounded-lg">
                                <h4 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Kontrak</h4>
                                @if($project->latestContract)
                                    <p class="text-sm text-gray-600 dark:text-gray-400">
                                        {{ $project->latestContract->spk_number ?? 'SPK Tersedia' }}
                                    </p>
                                    <p class="text-xs text-gray-500 dark:text-gray-500 mt-1">
                                        Ditandatangani: {{ $project->latestContract->signed_date->format('d M Y') }}
                                    </p>
                                @else
                                    <p class="text-sm text-gray-400 dark:text-gray-500 italic">Belum ada kontrak</p>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Sidebar --}}
                <div class="space-y-6">
                    {{-- Client Info --}}
                    <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-6">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Klien</h3>
                        @if($project->client)
                        <div class="flex items-center gap-3 mb-4">
                            <div class="w-12 h-12 rounded-full bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center">
                                <span class="text-lg font-semibold text-blue-600 dark:text-blue-400">
                                    {{ strtoupper(substr($project->client->name, 0, 2)) }}
                                </span>
                            </div>
                            <div>
                                <p class="font-medium text-gray-900 dark:text-white">{{ $project->client->name }}</p>
                                @if($project->client->company_name)
                                <p class="text-sm text-gray-500 dark:text-gray-400">{{ $project->client->company_name }}</p>
                                @endif
                            </div>
                        </div>
                        <dl class="space-y-2 text-sm">
                            @if($project->client->email)
                            <div class="flex items-center gap-2">
                                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                </svg>
                                <span class="text-gray-600 dark:text-gray-400">{{ $project->client->email }}</span>
                            </div>
                            @endif
                            @if($project->client->phone)
                            <div class="flex items-center gap-2">
                                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                                </svg>
                                <span class="text-gray-600 dark:text-gray-400">{{ $project->client->phone }}</span>
                            </div>
                            @endif
                        </dl>
                        <a href="{{ route('appraisal.clients.show', $project->client) }}" class="btn btn-ghost btn-sm w-full mt-4">
                            Lihat Detail Klien
                        </a>
                        @else
                        <p class="text-gray-500 dark:text-gray-400 text-sm">Tidak ada data klien</p>
                        @endif
                    </div>

                    {{-- Quick Actions --}}
                    <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-6">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Aksi Cepat</h3>
                        <div class="space-y-2">
                            <a href="{{ route('appraisal.assets.create', ['project_id' => $project->id]) }}" class="btn btn-outline btn-sm w-full justify-start gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                </svg>
                                Tambah Objek Penilaian
                            </a>
                            <a href="{{ route('appraisal.projects.kanban', $project) }}" class="btn btn-outline btn-sm w-full justify-start gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17V7m0 10a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2h2a2 2 0 012 2m0 10a2 2 0 002 2h2a2 2 0 002-2M9 7a2 2 0 012-2h2a2 2 0 012 2m0 10V7m0 10a2 2 0 002 2h2a2 2 0 002-2V7a2 2 0 00-2-2h-2a2 2 0 00-2 2" />
                                </svg>
                                Lihat Kanban Board
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Assets Tab --}}
        <div x-show="activeTab === 'assets'" x-cloak>
            <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700">
                <div class="p-4 border-b border-gray-200 dark:border-gray-700 flex justify-between items-center">
                    <h3 class="font-semibold text-gray-900 dark:text-white">Daftar Objek Penilaian</h3>
                    <a href="{{ route('appraisal.assets.create', ['project_id' => $project->id]) }}" class="btn btn-primary btn-sm">
                        + Tambah Objek
                    </a>
                </div>
                @if($project->assets->count() > 0)
                <div class="overflow-x-auto">
                    <table class="table table-zebra w-full">
                        <thead>
                            <tr>
                                <th>Kode</th>
                                <th>Nama Objek</th>
                                <th>Tipe</th>
                                <th>Stage</th>
                                <th>Target</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($project->assets as $asset)
                            <tr>
                                <td class="font-mono text-sm">{{ $asset->asset_code }}</td>
                                <td>{{ $asset->name }}</td>
                                <td>
                                    <span class="badge badge-ghost badge-sm">
                                        {{ ProjectAsset::ASSET_TYPES[$asset->asset_type] ?? $asset->asset_type }}
                                    </span>
                                </td>
                                <td>
                                    @php
                                        $stageColor = match($asset->current_stage) {
                                            'done' => 'badge-success',
                                            'final_report', 'client_approval' => 'badge-info',
                                            'review' => 'badge-warning',
                                            'analysis', 'inspection' => 'badge-primary',
                                            default => 'badge-ghost'
                                        };
                                    @endphp
                                    <span class="badge {{ $stageColor }} badge-sm">
                                        {{ ProjectAsset::STAGES[$asset->current_stage] ?? $asset->current_stage }}
                                    </span>
                                </td>
                                <td class="text-sm">
                                    @if($asset->target_completion_date)
                                        {{ $asset->target_completion_date->format('d M Y') }}
                                    @else
                                        -
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ route('appraisal.assets.show', $asset) }}" class="btn btn-ghost btn-xs">
                                        Detail
                                    </a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <div class="p-8 text-center">
                    <svg class="w-12 h-12 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                    </svg>
                    <p class="text-gray-500 dark:text-gray-400 mb-4">Belum ada objek penilaian</p>
                    <a href="{{ route('appraisal.assets.create', ['project_id' => $project->id]) }}" class="btn btn-primary btn-sm">
                        Tambah Objek Pertama
                    </a>
                </div>
                @endif
            </div>
        </div>

        {{-- Invoices Tab --}}
        <div x-show="activeTab === 'invoices'" x-cloak>
            <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Daftar Invoice</h3>
                @if($project->invoices && $project->invoices->count() > 0)
                <div class="space-y-4">
                    @foreach($project->invoices as $invoice)
                    <div class="p-4 bg-gray-50 dark:bg-gray-900/50 rounded-lg flex justify-between items-center">
                        <div>
                            <p class="font-medium">{{ $invoice->invoice_number }}</p>
                            <p class="text-sm text-gray-500">Jatuh tempo: {{ $invoice->payment_due_date->format('d M Y') }}</p>
                        </div>
                        <span class="badge {{ $invoice->status === 'paid' ? 'badge-success' : ($invoice->status === 'cancelled' ? 'badge-error' : 'badge-warning') }}">
                            {{ $invoice->status === 'paid' ? 'Lunas' : ($invoice->status === 'cancelled' ? 'Dibatalkan' : 'Belum Bayar') }}
                        </span>
                    </div>
                    @endforeach
                </div>
                @else
                <p class="text-gray-500 dark:text-gray-400 text-center py-8">Belum ada invoice</p>
                @endif
            </div>
        </div>

        {{-- Activities Tab --}}
        <div x-show="activeTab === 'activities'" x-cloak>
            <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Riwayat Aktivitas</h3>
                @if($project->activities && $project->activities->count() > 0)
                <div class="space-y-4">
                    @foreach($project->activities()->latest()->take(20)->get() as $activity)
                    <div class="flex gap-4">
                        <div class="w-8 h-8 rounded-full bg-gray-100 dark:bg-gray-700 flex items-center justify-center flex-shrink-0">
                            @if($activity->activity_type === 'stage_move')
                                <svg class="w-4 h-4 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6" />
                                </svg>
                            @elseif($activity->activity_type === 'comment')
                                <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                                </svg>
                            @elseif($activity->activity_type === 'obstacle')
                                <svg class="w-4 h-4 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                </svg>
                            @else
                                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            @endif
                        </div>
                        <div class="flex-1">
                            <p class="text-sm text-gray-900 dark:text-white">{{ $activity->description }}</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                {{ $activity->user->name ?? 'System' }} • {{ $activity->created_at->diffForHumans() }}
                            </p>
                        </div>
                    </div>
                    @endforeach
                </div>
                @else
                <p class="text-gray-500 dark:text-gray-400 text-center py-8">Belum ada aktivitas</p>
                @endif
            </div>
        </div>
    </div>

    {{-- Change Status Modal --}}
    <div x-show="showStatusModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center">
        <div class="absolute inset-0 bg-black/50" @click="showStatusModal = false"></div>
        <div class="relative bg-white dark:bg-gray-800 rounded-lg shadow-xl max-w-md w-full mx-4">
            <div class="p-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Ubah Status Proyek</h3>
                <form action="{{ route('appraisal.projects.update-status', $project) }}" method="POST">
                    @csrf
                    @method('PATCH')
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Status Baru</label>
                        <select name="status" class="select select-bordered w-full">
                            @foreach(ProjectKanban::STATUS as $key => $label)
                            <option value="{{ $key }}" {{ $project->status === $key ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="flex justify-end gap-2">
                        <button type="button" @click="showStatusModal = false" class="btn btn-ghost">Batal</button>
                        <button type="submit" class="btn btn-primary">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Delete Modal --}}
    <div x-show="showDeleteModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center">
        <div class="absolute inset-0 bg-black/50" @click="showDeleteModal = false"></div>
        <div class="relative bg-white dark:bg-gray-800 rounded-lg shadow-xl max-w-md w-full mx-4">
            <div class="p-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Hapus Proyek</h3>
                <p class="text-gray-600 dark:text-gray-400 mb-4">Apakah Anda yakin ingin menghapus proyek ini? Tindakan ini tidak dapat dibatalkan.</p>
                <form action="{{ route('appraisal.projects.destroy', $project) }}" method="POST">
                    @csrf
                    @method('DELETE')
                    <div class="flex justify-end gap-2">
                        <button type="button" @click="showDeleteModal = false" class="btn btn-ghost">Batal</button>
                        <button type="submit" class="btn btn-error">Hapus</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@extends('layouts.app')

@php
use App\Models\ProjectKanban;
@endphp

@section('content')
<div x-data="{ 
    activeTab: 'overview',
    showMoveModal: false,
    showDeleteModal: false
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
                    @if($project->priority_status === 'critical')
                    <span class="inline-flex items-center gap-1 rounded-full bg-red-100 px-2.5 py-1 text-xs font-medium text-red-700 dark:bg-red-900/30 dark:text-red-400">
                        🔴 Kritis
                    </span>
                    @elseif($project->priority_status === 'warning')
                    <span class="inline-flex items-center gap-1 rounded-full bg-yellow-100 px-2.5 py-1 text-xs font-medium text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-400">
                        🟡 Perhatian
                    </span>
                    @endif
                </div>
                <p class="text-gray-600 dark:text-gray-400 mt-1">{{ $project->project_code }}</p>
            </div>

            {{-- Actions --}}
            <div class="flex items-center gap-2">
                <button @click="showMoveModal = true" class="btn btn-outline btn-sm gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4" />
                    </svg>
                    Pindah Stage
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

    {{-- Stage Progress --}}
    <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-4 mb-6">
        <div class="flex items-center justify-between">
            @php
                $stages = ProjectKanban::STAGES;
                $currentStageIndex = array_search($project->current_stage, array_keys($stages));
            @endphp
            
            @foreach($stages as $key => $stageLabel)
                @php
                    $stageIndex = array_search($key, array_keys($stages));
                    $isCompleted = $stageIndex < $currentStageIndex;
                    $isCurrent = $key === $project->current_stage;
                @endphp
                <div class="flex-1 relative">
                    <div class="flex items-center">
                        <div class="w-8 h-8 rounded-full flex items-center justify-center text-xs font-medium
                            {{ $isCompleted ? 'bg-green-500 text-white' : ($isCurrent ? 'bg-blue-500 text-white' : 'bg-gray-200 dark:bg-gray-700 text-gray-500 dark:text-gray-400') }}">
                            @if($isCompleted)
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                </svg>
                            @else
                                {{ $stageIndex + 1 }}
                            @endif
                        </div>
                        @if(!$loop->last)
                            <div class="flex-1 h-1 mx-2 {{ $isCompleted ? 'bg-green-500' : 'bg-gray-200 dark:bg-gray-700' }}"></div>
                        @endif
                    </div>
                    <span class="absolute -bottom-6 left-0 text-xs {{ $isCurrent ? 'text-blue-600 dark:text-blue-400 font-medium' : 'text-gray-500 dark:text-gray-400' }}">
                        {{ $stageLabel }}
                    </span>
                </div>
            @endforeach
        </div>
    </div>

    {{-- Tabs --}}
    <div class="border-b border-gray-200 dark:border-gray-700 mb-6 mt-10">
        <nav class="-mb-px flex space-x-6 overflow-x-auto">
            <button @click="activeTab = 'overview'" 
                    :class="{ 'border-blue-500 text-blue-600 dark:text-blue-400': activeTab === 'overview' }"
                    class="py-3 px-1 border-b-2 border-transparent text-sm font-medium text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300 whitespace-nowrap">
                Overview
            </button>
            <button @click="activeTab === 'inspections'" 
                    :class="{ 'border-blue-500 text-blue-600 dark:text-blue-400': activeTab === 'inspections' }"
                    class="py-3 px-1 border-b-2 border-transparent text-sm font-medium text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300 whitespace-nowrap">
                Inspeksi
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
                            <div>
                                <dt class="text-sm text-gray-500 dark:text-gray-400">Lokasi</dt>
                                <dd class="text-sm font-medium text-gray-900 dark:text-white">{{ $project->location ?? '-' }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm text-gray-500 dark:text-gray-400">Target Selesai</dt>
                                <dd class="text-sm font-medium text-gray-900 dark:text-white">
                                    @if($project->due_date)
                                        {{ $project->due_date->format('d M Y') }}
                                        @if($project->due_date->isPast())
                                            <span class="text-red-500 text-xs">(Lewat)</span>
                                        @elseif($project->due_date->diffInDays(now()) <= 7)
                                            <span class="text-yellow-500 text-xs">({{ $project->due_date->diffForHumans() }})</span>
                                        @endif
                                    @else
                                        -
                                    @endif
                                </dd>
                            </div>
                            <div>
                                <dt class="text-sm text-gray-500 dark:text-gray-400">Stage Saat Ini</dt>
                                <dd class="text-sm">
                                    @php
                                        $currentStageLabel = ProjectKanban::STAGES[$project->current_stage] ?? null;
                                        $stageColors = [
                                            'lead' => 'badge-ghost',
                                            'proposal' => 'badge-info',
                                            'contract' => 'badge-primary',
                                            'inspection' => 'badge-secondary',
                                            'analysis' => 'badge-accent',
                                            'review' => 'badge-warning',
                                            'client_approval' => 'badge-warning',
                                            'final_report' => 'badge-info',
                                            'invoicing' => 'badge-success',
                                            'done' => 'badge-success',
                                        ];
                                    @endphp
                                    <span class="badge {{ $stageColors[$project->current_stage] ?? 'badge-ghost' }}">{{ $currentStageLabel ?? $project->current_stage }}</span>
                                </dd>
                            </div>
                            <div>
                                <dt class="text-sm text-gray-500 dark:text-gray-400">Status Prioritas</dt>
                                <dd class="text-sm">
                                    @if($project->priority_status === 'critical')
                                        <span class="badge badge-error">Kritis</span>
                                    @elseif($project->priority_status === 'warning')
                                        <span class="badge badge-warning">Perhatian</span>
                                    @else
                                        <span class="badge badge-success">Normal</span>
                                    @endif
                                </dd>
                            </div>
                        </dl>
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
                            @if($project->client->address)
                            <div class="flex items-start gap-2">
                                <svg class="w-4 h-4 text-gray-400 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                                <span class="text-gray-600 dark:text-gray-400">{{ $project->client->address }}</span>
                            </div>
                            @endif
                        </dl>
                        <a href="{{ route('appraisal.clients.show', $project->client) }}" class="mt-4 text-sm text-blue-600 dark:text-blue-400 hover:underline inline-flex items-center gap-1">
                            Lihat Detail Klien
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                            </svg>
                        </a>
                        @else
                        <p class="text-gray-500 dark:text-gray-400">Belum ada klien</p>
                        @endif
                    </div>

                    {{-- Quick Stats --}}
                    <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-6">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Statistik</h3>
                        <dl class="space-y-3">
                            <div class="flex justify-between">
                                <dt class="text-sm text-gray-500 dark:text-gray-400">Inspeksi</dt>
                                <dd class="text-sm font-medium text-gray-900 dark:text-white">{{ $project->inspections->count() }}</dd>
                            </div>
                            <div class="flex justify-between">
                                <dt class="text-sm text-gray-500 dark:text-gray-400">Invoice</dt>
                                <dd class="text-sm font-medium text-gray-900 dark:text-white">{{ $project->invoices->count() }}</dd>
                            </div>
                            <div class="flex justify-between">
                                <dt class="text-sm text-gray-500 dark:text-gray-400">Aktivitas</dt>
                                <dd class="text-sm font-medium text-gray-900 dark:text-white">{{ $project->activities->count() }}</dd>
                            </div>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        {{-- Inspections Tab --}}
        <div x-show="activeTab === 'inspections'" x-cloak>
            <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Daftar Inspeksi</h3>
                    <a href="{{ route('appraisal.inspections.create', $project) }}" class="btn btn-primary btn-sm">Tambah Inspeksi</a>
                </div>
                @if($project->inspections->count() > 0)
                <div class="space-y-3">
                    @foreach($project->inspections as $inspection)
                    <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                        <div>
                            <p class="font-medium text-gray-900 dark:text-white">{{ $inspection->inspection_date?->format('d M Y') ?? 'Tanggal tidak diset' }}</p>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Surveyor: {{ $inspection->surveyor->name ?? '-' }}</p>
                        </div>
                        <a href="{{ route('appraisal.inspections.show', $inspection) }}" class="btn btn-ghost btn-sm">Detail</a>
                    </div>
                    @endforeach
                </div>
                @else
                <p class="text-gray-500 dark:text-gray-400 text-center py-8">Belum ada inspeksi untuk proyek ini</p>
                @endif
            </div>
        </div>

        {{-- Invoices Tab --}}
        <div x-show="activeTab === 'invoices'" x-cloak>
            <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Daftar Invoice</h3>
                    <a href="{{ route('appraisal.invoices.create', $project) }}" class="btn btn-primary btn-sm">Buat Invoice</a>
                </div>
                @if($project->invoices->count() > 0)
                <div class="space-y-3">
                    @foreach($project->invoices as $invoice)
                    <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                        <div>
                            <p class="font-medium text-gray-900 dark:text-white font-mono">{{ $invoice->invoice_number }}</p>
                            <p class="text-sm text-gray-500 dark:text-gray-400">
                                Jatuh tempo: {{ $invoice->payment_due_date?->format('d M Y') ?? '-' }}
                            </p>
                        </div>
                        <div class="flex items-center gap-2">
                            @if($invoice->status === 'paid')
                                <span class="badge badge-success">Lunas</span>
                            @elseif($invoice->status === 'cancelled')
                                <span class="badge badge-ghost">Dibatalkan</span>
                            @else
                                <span class="badge badge-warning">Belum Dibayar</span>
                            @endif
                            <a href="{{ route('appraisal.invoices.show', $invoice) }}" class="btn btn-ghost btn-sm">Detail</a>
                        </div>
                    </div>
                    @endforeach
                </div>
                @else
                <p class="text-gray-500 dark:text-gray-400 text-center py-8">Belum ada invoice untuk proyek ini</p>
                @endif
            </div>
        </div>

        {{-- Activities Tab --}}
        <div x-show="activeTab === 'activities'" x-cloak>
            <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Aktivitas Terbaru</h3>
                @if($project->activities->count() > 0)
                <div class="space-y-4">
                    @foreach($project->activities->take(20) as $activity)
                    <div class="flex gap-3">
                        <div class="w-8 h-8 rounded-full bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center flex-shrink-0">
                            <span class="text-xs font-medium text-blue-600 dark:text-blue-400">
                                {{ substr($activity->user->name ?? 'S', 0, 1) }}
                            </span>
                        </div>
                        <div class="flex-1">
                            <p class="text-sm text-gray-900 dark:text-white">
                                <span class="font-medium">{{ $activity->user->name ?? 'System' }}</span>
                                {{ $activity->description }}
                            </p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">
                                {{ $activity->created_at->format('d M Y H:i') }} • {{ $activity->activity_type }}
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

    {{-- Move Stage Modal --}}
    <div x-show="showMoveModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center">
        <div class="absolute inset-0 bg-black/50" @click="showMoveModal = false"></div>
        <div class="relative bg-white dark:bg-gray-800 rounded-lg shadow-xl max-w-md w-full mx-4">
            <div class="p-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Pindah Stage</h3>
                <form action="{{ route('appraisal.projects.move-stage', $project) }}" method="POST">
                    @csrf
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Stage Baru</label>
                        <select name="stage" class="select select-bordered w-full">
                            @foreach(ProjectKanban::STAGES as $key => $label)
                            <option value="{{ $key }}" {{ $project->current_stage === $key ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="flex justify-end gap-2">
                        <button type="button" @click="showMoveModal = false" class="btn btn-ghost">Batal</button>
                        <button type="submit" class="btn btn-primary">Pindah</button>
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

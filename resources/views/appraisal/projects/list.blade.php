@extends('layouts.app')

@php
use App\Models\ProjectKanban;
@endphp

@section('content')
<div>
    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Daftar Proyek</h1>
            <p class="text-gray-600 dark:text-gray-400 mt-1">Kelola semua proyek penilaian</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('appraisal.projects.index') }}" class="btn btn-outline btn-sm gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17V7m0 10a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2h2a2 2 0 012 2m0 10a2 2 0 002 2h2a2 2 0 002-2M9 7a2 2 0 012-2h2a2 2 0 012 2m0 10V7m0 10a2 2 0 002 2h2a2 2 0 002-2V7a2 2 0 00-2-2h-2a2 2 0 00-2 2" />
                </svg>
                Kanban View
            </a>
            <a href="{{ route('appraisal.projects.create') }}" class="btn btn-primary btn-sm gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Proyek Baru
            </a>
        </div>
    </div>

    {{-- Filters --}}
    <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-4 mb-6">
        <form action="{{ route('appraisal.projects.list') }}" method="GET" class="flex flex-col lg:flex-row gap-4">
            <div class="flex-1">
                <input type="text" name="search" value="{{ request('search') }}" 
                       placeholder="Cari kode proyek, nama, atau lokasi..." 
                       class="input input-bordered w-full">
            </div>
            <select name="stage" class="select select-bordered w-full lg:w-40">
                <option value="">Semua Stage</option>
                @foreach(ProjectKanban::STAGES as $key => $label)
                <option value="{{ $key }}" {{ request('stage') === $key ? 'selected' : '' }}>{{ $label }}</option>
                @endforeach
            </select>
            <select name="client_id" class="select select-bordered w-full lg:w-48">
                <option value="">Semua Klien</option>
                @foreach($clients ?? [] as $client)
                <option value="{{ $client->id }}" {{ request('client_id') == $client->id ? 'selected' : '' }}>{{ $client->name }}</option>
                @endforeach
            </select>
            <select name="priority" class="select select-bordered w-full lg:w-36">
                <option value="">Semua Prioritas</option>
                <option value="critical" {{ request('priority') === 'critical' ? 'selected' : '' }}>🔴 Kritis</option>
                <option value="warning" {{ request('priority') === 'warning' ? 'selected' : '' }}>🟡 Perhatian</option>
                <option value="normal" {{ request('priority') === 'normal' ? 'selected' : '' }}>Normal</option>
            </select>
            <button type="submit" class="btn btn-primary">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
            </button>
            @if(request()->hasAny(['search', 'stage', 'client_id', 'priority']))
            <a href="{{ route('appraisal.projects.list') }}" class="btn btn-ghost">Reset</a>
            @endif
        </form>
    </div>

    {{-- Projects Table --}}
    <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="table w-full">
                <thead>
                    <tr>
                        <th>
                            <a href="{{ request()->fullUrlWithQuery(['sort' => 'project_code', 'direction' => request('direction') === 'asc' ? 'desc' : 'asc']) }}" 
                               class="flex items-center gap-1 hover:text-blue-600">
                                Kode Proyek
                                @if(request('sort') === 'project_code')
                                <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                    @if(request('direction') === 'asc')
                                    <path fill-rule="evenodd" d="M5.293 7.707a1 1 0 010-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 01-1.414 1.414L10 4.414l-3.293 3.293a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                                    @else
                                    <path fill-rule="evenodd" d="M14.707 12.293a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 111.414-1.414L10 15.586l3.293-3.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                    @endif
                                </svg>
                                @endif
                            </a>
                        </th>
                        <th>Proyek</th>
                        <th>Klien</th>
                        <th>Lokasi</th>
                        <th>Stage</th>
                        <th>
                            <a href="{{ request()->fullUrlWithQuery(['sort' => 'due_date', 'direction' => request('direction') === 'asc' ? 'desc' : 'asc']) }}" 
                               class="flex items-center gap-1 hover:text-blue-600">
                                Deadline
                                @if(request('sort') === 'due_date')
                                <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                    @if(request('direction') === 'asc')
                                    <path fill-rule="evenodd" d="M5.293 7.707a1 1 0 010-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 01-1.414 1.414L10 4.414l-3.293 3.293a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                                    @else
                                    <path fill-rule="evenodd" d="M14.707 12.293a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 111.414-1.414L10 15.586l3.293-3.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                    @endif
                                </svg>
                                @endif
                            </a>
                        </th>
                        <th class="text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($projects as $project)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                        <td>
                            <div class="flex items-center gap-2">
                                @if($project->priority_status === 'critical')
                                <span class="w-2 h-2 rounded-full bg-red-500"></span>
                                @elseif($project->priority_status === 'warning')
                                <span class="w-2 h-2 rounded-full bg-yellow-500"></span>
                                @endif
                                <span class="font-mono text-sm text-gray-600 dark:text-gray-400">{{ $project->project_code }}</span>
                            </div>
                        </td>
                        <td>
                            <a href="{{ route('appraisal.projects.show', $project) }}" class="font-medium text-gray-900 dark:text-white hover:text-blue-600 dark:hover:text-blue-400">
                                {{ Str::limit($project->name, 40) }}
                            </a>
                        </td>
                        <td>
                            @if($project->client)
                            <div class="flex items-center gap-2">
                                <div class="w-7 h-7 rounded-full bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center">
                                    <span class="text-xs font-medium text-blue-600 dark:text-blue-400">
                                        {{ substr($project->client->name, 0, 1) }}
                                    </span>
                                </div>
                                <span class="text-sm text-gray-600 dark:text-gray-400">{{ Str::limit($project->client->name, 20) }}</span>
                            </div>
                            @else
                            <span class="text-gray-400">-</span>
                            @endif
                        </td>
                        <td>
                            <span class="text-sm text-gray-600 dark:text-gray-400">
                                {{ $project->location ? Str::limit($project->location, 30) : '-' }}
                            </span>
                        </td>
                        <td>
                            @php
                                $stageLabel = ProjectKanban::STAGES[$project->current_stage] ?? null;
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
                            <span class="badge {{ $stageColors[$project->current_stage] ?? 'badge-ghost' }} badge-sm">
                                {{ $stageLabel ?? $project->current_stage }}
                            </span>
                        </td>
                        <td>
                            @if($project->due_date)
                            <span class="text-sm {{ $project->due_date->isPast() ? 'text-red-600 dark:text-red-400 font-medium' : ($project->due_date->diffInDays(now()) <= 3 ? 'text-yellow-600 dark:text-yellow-400' : 'text-gray-600 dark:text-gray-400') }}">
                                {{ $project->due_date->format('d M Y') }}
                                @if($project->due_date->isPast())
                                <span class="text-xs">(Overdue)</span>
                                @endif
                            </span>
                            @else
                            <span class="text-gray-400">-</span>
                            @endif
                        </td>
                        <td class="text-right">
                            <div class="dropdown dropdown-end">
                                <label tabindex="0" class="btn btn-ghost btn-sm btn-square">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z" />
                                    </svg>
                                </label>
                                <ul tabindex="0" class="dropdown-content z-[1] menu p-2 shadow bg-base-100 rounded-box w-48">
                                    <li><a href="{{ route('appraisal.projects.show', $project) }}">Lihat Detail</a></li>
                                    <li><a href="{{ route('appraisal.projects.edit', $project) }}">Edit</a></li>
                                    <li class="divider"></li>
                                    <li>
                                        <form action="{{ route('appraisal.projects.destroy', $project) }}" method="POST" 
                                              onsubmit="return confirm('Apakah Anda yakin ingin menghapus proyek ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-error w-full text-left">Hapus</button>
                                        </form>
                                    </li>
                                </ul>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-12">
                            <svg class="w-12 h-12 mx-auto text-gray-300 dark:text-gray-600 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                            </svg>
                            <p class="text-gray-500 dark:text-gray-400 mb-4">Tidak ada proyek ditemukan</p>
                            <a href="{{ route('appraisal.projects.create') }}" class="btn btn-primary btn-sm">Buat Proyek Baru</a>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if($projects->hasPages())
        <div class="px-4 py-3 border-t border-gray-200 dark:border-gray-700">
            {{ $projects->links() }}
        </div>
        @endif
    </div>
</div>
@endsection

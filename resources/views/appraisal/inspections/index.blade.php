@extends('layouts.app')

@section('content')
<div>
    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Daftar Inspeksi</h1>
            <p class="text-gray-600 dark:text-gray-400 mt-1">Kelola jadwal dan data inspeksi lapangan</p>
        </div>
        <div class="dropdown dropdown-end">
            <label tabindex="0" class="btn btn-primary gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Jadwalkan Inspeksi
            </label>
            <ul tabindex="0" class="dropdown-content z-[1] menu p-2 shadow bg-base-100 rounded-box w-72 max-h-96 overflow-y-auto">
                <li class="menu-title">Pilih Proyek</li>
                @forelse($projects ?? [] as $project)
                <li>
                    <a href="{{ route('appraisal.inspections.create', $project) }}">
                        <span class="font-mono text-xs">{{ $project->project_code }}</span>
                        <span class="truncate">{{ Str::limit($project->name, 25) }}</span>
                    </a>
                </li>
                @empty
                <li class="disabled"><span class="text-gray-400">Tidak ada proyek tersedia</span></li>
                @endforelse
            </ul>
        </div>
    </div>

    {{-- Stats Cards --}}
    <div class="grid grid-cols-2 md:grid-cols-3 gap-4 mb-6">
        <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-4">
            <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">Total Inspeksi</p>
            <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $inspections->total() }}</p>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-4">
            <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">Minggu Ini</p>
            <p class="text-2xl font-bold text-blue-600 dark:text-blue-400">{{ $inspections->where('inspection_date', '>=', now()->startOfWeek())->count() }}</p>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-4">
            <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">Dengan Koordinat</p>
            <p class="text-2xl font-bold text-green-600 dark:text-green-400">{{ $inspections->whereNotNull('latitude')->count() }}</p>
        </div>
    </div>

    {{-- Filters --}}
    <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-4 mb-6">
        <form action="{{ route('appraisal.inspections.index') }}" method="GET" class="flex flex-col lg:flex-row gap-4">
            <div class="flex-1">
                <input type="text" name="search" value="{{ request('search') }}" 
                       placeholder="Cari proyek atau surveyor..." 
                       class="input input-bordered w-full">
            </div>
            <input type="date" name="date_from" value="{{ request('date_from') }}" 
                   class="input input-bordered w-full lg:w-40" placeholder="Dari tanggal">
            <input type="date" name="date_to" value="{{ request('date_to') }}" 
                   class="input input-bordered w-full lg:w-40" placeholder="Sampai tanggal">
            <button type="submit" class="btn btn-primary">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
            </button>
            @if(request()->hasAny(['search', 'date_from', 'date_to']))
            <a href="{{ route('appraisal.inspections.index') }}" class="btn btn-ghost">Reset</a>
            @endif
        </form>
    </div>

    {{-- Inspections Table --}}
    <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="table w-full">
                <thead>
                    <tr>
                        <th>Proyek</th>
                        <th>Tanggal Inspeksi</th>
                        <th>Surveyor</th>
                        <th>Koordinat</th>
                        <th>Catatan</th>
                        <th class="text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($inspections as $inspection)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                        <td>
                            <div>
                                <a href="{{ route('appraisal.projects.show', $inspection->project) }}" class="font-medium text-gray-900 dark:text-white hover:text-blue-600 dark:hover:text-blue-400">
                                    {{ $inspection->project->project_code ?? '-' }}
                                </a>
                                <p class="text-xs text-gray-500 dark:text-gray-400">{{ Str::limit($inspection->project->name ?? '', 30) }}</p>
                            </div>
                        </td>
                        <td>
                            @if($inspection->inspection_date)
                            <div class="text-sm">
                                <p class="font-medium {{ $inspection->inspection_date->isToday() ? 'text-blue-600 dark:text-blue-400' : ($inspection->inspection_date->isPast() ? 'text-gray-600 dark:text-gray-400' : 'text-gray-900 dark:text-white') }}">
                                    {{ $inspection->inspection_date->format('d M Y') }}
                                    @if($inspection->inspection_date->isToday())
                                    <span class="badge badge-info badge-xs ml-1">Hari Ini</span>
                                    @endif
                                </p>
                            </div>
                            @else
                            <span class="text-gray-400">Belum dijadwalkan</span>
                            @endif
                        </td>
                        <td>
                            @if($inspection->surveyor)
                            <div class="flex items-center gap-2">
                                <div class="w-7 h-7 rounded-full bg-green-100 dark:bg-green-900/30 flex items-center justify-center">
                                    <span class="text-xs font-medium text-green-600 dark:text-green-400">
                                        {{ substr($inspection->surveyor->name, 0, 1) }}
                                    </span>
                                </div>
                                <span class="text-sm text-gray-600 dark:text-gray-400">{{ $inspection->surveyor->name }}</span>
                            </div>
                            @else
                            <span class="text-gray-400">-</span>
                            @endif
                        </td>
                        <td>
                            @if($inspection->latitude && $inspection->longitude)
                            <a href="https://www.google.com/maps?q={{ $inspection->latitude }},{{ $inspection->longitude }}" 
                               target="_blank"
                               class="text-sm text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300 flex items-center gap-1">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                                <span>Lihat Map</span>
                            </a>
                            @else
                            <span class="text-gray-400 text-sm">Tidak ada</span>
                            @endif
                        </td>
                        <td>
                            <span class="text-sm text-gray-600 dark:text-gray-400">
                                {{ $inspection->notes ? Str::limit($inspection->notes, 30) : '-' }}
                            </span>
                        </td>
                        <td class="text-right">
                            <div class="dropdown dropdown-end">
                                <label tabindex="0" class="btn btn-ghost btn-sm btn-square">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z" />
                                    </svg>
                                </label>
                                <ul tabindex="0" class="dropdown-content z-[1] menu p-2 shadow bg-base-100 rounded-box w-48">
                                    <li><a href="{{ route('appraisal.inspections.show', $inspection) }}">Lihat Detail</a></li>
                                    <li><a href="{{ route('appraisal.inspections.edit', $inspection) }}">Edit</a></li>
                                    <li class="divider"></li>
                                    <li>
                                        <form action="{{ route('appraisal.inspections.destroy', $inspection) }}" method="POST" 
                                              onsubmit="return confirm('Apakah Anda yakin ingin menghapus inspeksi ini?')">
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
                        <td colspan="6" class="text-center py-12">
                            <svg class="w-12 h-12 mx-auto text-gray-300 dark:text-gray-600 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                            <p class="text-gray-500 dark:text-gray-400 mb-4">Belum ada data inspeksi</p>
                            <a href="{{ route('appraisal.projects.index') }}" class="btn btn-primary btn-sm">Pilih Proyek untuk Inspeksi</a>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if($inspections->hasPages())
        <div class="px-4 py-3 border-t border-gray-200 dark:border-gray-700">
            {{ $inspections->links() }}
        </div>
        @endif
    </div>
</div>
@endsection

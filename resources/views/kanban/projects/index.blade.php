@extends('layouts.app')

@section('title', 'Daftar Proyek')

@section('content')
<div>
    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Daftar Proyek</h1>
            <p class="text-gray-600 dark:text-gray-400 mt-1">Kelola proyek penilaian</p>
        </div>
        <a href="{{ route('kanban.projects.create') }}" class="inline-flex items-center gap-2 px-4 py-2.5 bg-brand-500 hover:bg-brand-600 text-white rounded-lg font-medium text-sm transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            Proyek Baru
        </a>
    </div>

    {{-- Alert Messages --}}
    @if(session('success'))
    <div class="mb-6 p-4 rounded-lg bg-green-50 border border-green-200 dark:bg-green-900/20 dark:border-green-800">
        <p class="text-green-700 dark:text-green-400">{{ session('success') }}</p>
    </div>
    @endif

    {{-- Filters --}}
    <div class="bg-white dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-800 p-4 mb-6">
        <form action="{{ route('kanban.projects.index') }}" method="GET" class="flex flex-col sm:flex-row gap-4">
            <div class="flex-1">
                <input type="text" name="search" value="{{ request('search') }}" 
                       placeholder="Cari nama proyek, kode..." 
                       class="w-full px-4 py-2 rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-brand-500">
            </div>
            <select name="status" class="px-4 py-2 rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-900 dark:text-white">
                <option value="">Semua Status</option>
                <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Aktif</option>
                <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Selesai</option>
                <option value="on_hold" {{ request('status') === 'on_hold' ? 'selected' : '' }}>Ditunda</option>
                <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>Dibatalkan</option>
            </select>
            <button type="submit" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 dark:bg-gray-800 dark:hover:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-lg transition">
                Filter
            </button>
            @if(request()->hasAny(['search', 'status']))
            <a href="{{ route('kanban.projects.index') }}" class="px-4 py-2 text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white transition">Reset</a>
            @endif
        </form>
    </div>

    {{-- Stats Cards --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-white dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-800 p-4">
            <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">Total Proyek</p>
            <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $projects->total() }}</p>
        </div>
        <div class="bg-white dark:bg-gray-900 rounded-xl border border-green-200 dark:border-green-800/50 p-4">
            <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">Aktif</p>
            <p class="text-2xl font-bold text-green-600 dark:text-green-400">{{ $projects->where('status', 'active')->count() }}</p>
        </div>
        <div class="bg-white dark:bg-gray-900 rounded-xl border border-blue-200 dark:border-blue-800/50 p-4">
            <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">Selesai</p>
            <p class="text-2xl font-bold text-blue-600 dark:text-blue-400">{{ $projects->where('status', 'completed')->count() }}</p>
        </div>
        <div class="bg-white dark:bg-gray-900 rounded-xl border border-yellow-200 dark:border-yellow-800/50 p-4">
            <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">Ditunda</p>
            <p class="text-2xl font-bold text-yellow-600 dark:text-yellow-400">{{ $projects->where('status', 'on_hold')->count() }}</p>
        </div>
    </div>

    {{-- Project List --}}
    <div class="bg-white dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-800 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 dark:bg-gray-800">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Proyek</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Klien</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Asset</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Due Date</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-800">
                    @forelse($projects as $project)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/50 transition">
                        <td class="px-6 py-4">
                            <div>
                                <a href="{{ route('kanban.projects.show', $project) }}" class="font-medium text-gray-900 dark:text-white hover:text-brand-600 dark:hover:text-brand-400">
                                    {{ $project->name }}
                                </a>
                                <p class="text-xs text-gray-500 dark:text-gray-400">{{ $project->project_code }}</p>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            @if($project->client)
                            <a href="{{ route('kanban.clients.show', $project->client) }}" class="text-sm text-gray-600 dark:text-gray-400 hover:text-brand-600 dark:hover:text-brand-400">
                                {{ $project->client->name }}
                            </a>
                            @else
                            <span class="text-sm text-gray-400">-</span>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                {{ $project->status === 'active' ? 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400' : '' }}
                                {{ $project->status === 'completed' ? 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400' : '' }}
                                {{ $project->status === 'on_hold' ? 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-400' : '' }}
                                {{ $project->status === 'cancelled' ? 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400' : '' }}
                            ">
                                {{ ucfirst(str_replace('_', ' ', $project->status)) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-400">
                            {{ $project->assets_count ?? 0 }} asset
                        </td>
                        <td class="px-6 py-4 text-sm">
                            @if($project->due_date)
                                @php $isOverdue = $project->due_date->isPast() && $project->status === 'active'; @endphp
                                <span class="{{ $isOverdue ? 'text-red-600 dark:text-red-400' : 'text-gray-600 dark:text-gray-400' }}">
                                    {{ $project->due_date->format('d M Y') }}
                                    @if($isOverdue)
                                    <span class="text-xs">(Lewat)</span>
                                    @endif
                                </span>
                            @else
                                <span class="text-gray-400">-</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-right">
                            <div class="flex items-center justify-end gap-2">
                                <a href="{{ route('kanban.projects.show', $project) }}" class="p-2 text-gray-500 hover:text-brand-600 dark:text-gray-400 dark:hover:text-brand-400 transition" title="Lihat">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    </svg>
                                </a>
                                <a href="{{ route('kanban.projects.edit', $project) }}" class="p-2 text-gray-500 hover:text-blue-600 dark:text-gray-400 dark:hover:text-blue-400 transition" title="Edit">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                    </svg>
                                </a>
                                @if(auth()->user()->hasAdminAccess())
                                <form action="{{ route('kanban.projects.destroy', $project) }}" method="POST" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus proyek ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-2 text-gray-500 hover:text-red-600 dark:text-gray-400 dark:hover:text-red-400 transition" title="Hapus">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                    </button>
                                </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center">
                            <svg class="w-12 h-12 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            <p class="text-gray-500 dark:text-gray-400">Belum ada proyek</p>
                            <a href="{{ route('kanban.projects.create') }}" class="mt-2 inline-block text-brand-600 hover:text-brand-700 dark:text-brand-400 font-medium">Buat proyek pertama →</a>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($projects->hasPages())
        <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-800">
            {{ $projects->withQueryString()->links() }}
        </div>
        @endif
    </div>
</div>
@endsection

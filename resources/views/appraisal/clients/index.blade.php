@extends('layouts.app')

@section('content')
<div>
    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Daftar Klien</h1>
            <p class="text-gray-600 dark:text-gray-400 mt-1">Kelola data klien perusahaan</p>
        </div>
        <a href="{{ route('appraisal.clients.create') }}" class="btn btn-primary gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            Tambah Klien
        </a>
    </div>

    {{-- Filters & Search --}}
    <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-4 mb-6">
        <form action="{{ route('appraisal.clients.index') }}" method="GET" class="flex flex-col sm:flex-row gap-4">
            <div class="flex-1">
                <input type="text" name="search" value="{{ request('search') }}" 
                       placeholder="Cari nama, perusahaan, email..." 
                       class="input input-bordered w-full">
            </div>
            <button type="submit" class="btn btn-primary">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
            </button>
            @if(request('search'))
            <a href="{{ route('appraisal.clients.index') }}" class="btn btn-ghost">Reset</a>
            @endif
        </form>
    </div>

    {{-- Stats Cards --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-4">
            <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">Total Klien</p>
            <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $clients->total() }}</p>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-4">
            <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">Dengan Proyek</p>
            <p class="text-2xl font-bold text-green-600 dark:text-green-400">{{ $clients->filter(fn($c) => $c->projects_count > 0)->count() }}</p>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-4">
            <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">Klien Baru (Bulan Ini)</p>
            <p class="text-2xl font-bold text-blue-600 dark:text-blue-400">{{ $clients->filter(fn($c) => $c->created_at->isCurrentMonth())->count() }}</p>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-4">
            <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">Total Proyek</p>
            <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $clients->sum('projects_count') }}</p>
        </div>
    </div>

    {{-- Client Table --}}
    <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="table w-full">
                <thead>
                    <tr>
                        <th>Klien</th>
                        <th>Kontak</th>
                        <th>Alamat</th>
                        <th>Proyek</th>
                        <th>Terdaftar</th>
                        <th class="text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($clients as $client)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                        <td>
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-full bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center">
                                    <span class="text-sm font-semibold text-blue-600 dark:text-blue-400">
                                        {{ strtoupper(substr($client->name, 0, 2)) }}
                                    </span>
                                </div>
                                <div>
                                    <a href="{{ route('appraisal.clients.show', $client) }}" class="font-medium text-gray-900 dark:text-white hover:text-blue-600 dark:hover:text-blue-400">
                                        {{ $client->name }}
                                    </a>
                                    @if($client->company_name)
                                    <p class="text-xs text-gray-500 dark:text-gray-400">{{ $client->company_name }}</p>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td>
                            <div class="text-sm">
                                @if($client->email)
                                <div class="flex items-center gap-1 text-gray-600 dark:text-gray-400">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                    </svg>
                                    <span>{{ $client->email }}</span>
                                </div>
                                @endif
                                @if($client->phone)
                                <div class="flex items-center gap-1 text-gray-600 dark:text-gray-400 mt-1">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                                    </svg>
                                    <span>{{ $client->phone }}</span>
                                </div>
                                @endif
                            </div>
                        </td>
                        <td>
                            <span class="text-sm text-gray-600 dark:text-gray-400">
                                {{ $client->address ? Str::limit($client->address, 30) : '-' }}
                            </span>
                        </td>
                        <td>
                            <div class="flex items-center gap-2">
                                <span class="font-medium text-gray-900 dark:text-white">{{ $client->projects_count ?? 0 }}</span>
                            </div>
                        </td>
                        <td class="text-sm text-gray-500 dark:text-gray-400">
                            {{ $client->created_at->format('d M Y') }}
                        </td>
                        <td class="text-right">
                            <div class="dropdown dropdown-end">
                                <label tabindex="0" class="btn btn-ghost btn-sm btn-square">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z" />
                                    </svg>
                                </label>
                                <ul tabindex="0" class="dropdown-content z-[1] menu p-2 shadow bg-base-100 rounded-box w-48">
                                    <li><a href="{{ route('appraisal.clients.show', $client) }}">Lihat Detail</a></li>
                                    <li><a href="{{ route('appraisal.clients.edit', $client) }}">Edit</a></li>
                                    <li><a href="{{ route('appraisal.projects.create', ['client_id' => $client->id]) }}">Buat Proyek Baru</a></li>
                                    @if($client->projects_count == 0)
                                    <li class="divider"></li>
                                    <li>
                                        <form action="{{ route('appraisal.clients.destroy', $client) }}" method="POST" 
                                              onsubmit="return confirm('Apakah Anda yakin ingin menghapus klien ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-error w-full text-left">Hapus</button>
                                        </form>
                                    </li>
                                    @endif
                                </ul>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center py-12">
                            <svg class="w-12 h-12 mx-auto text-gray-300 dark:text-gray-600 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                            </svg>
                            <p class="text-gray-500 dark:text-gray-400 mb-4">Belum ada data klien</p>
                            <a href="{{ route('appraisal.clients.create') }}" class="btn btn-primary btn-sm">Tambah Klien Pertama</a>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if($clients->hasPages())
        <div class="px-4 py-3 border-t border-gray-200 dark:border-gray-700">
            {{ $clients->links() }}
        </div>
        @endif
    </div>
</div>
@endsection

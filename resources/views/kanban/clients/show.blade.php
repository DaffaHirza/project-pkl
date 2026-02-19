@extends('layouts.app')

@section('title', 'Detail Klien - ' . $client->name)

@section('content')
<div class="space-y-6">
    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <a href="{{ route('kanban.clients.index') }}" class="inline-flex items-center text-sm text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300 mb-2">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
                Kembali ke Daftar
            </a>
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 rounded-full bg-brand-100 dark:bg-brand-900/30 flex items-center justify-center">
                    <span class="text-lg font-semibold text-brand-600 dark:text-brand-400">
                        {{ strtoupper(substr($client->name, 0, 2)) }}
                    </span>
                </div>
                <div>
                    <h1 class="text-2xl font-bold text-gray-900 dark:text-white">{{ $client->name }}</h1>
                    @if($client->company_name)
                    <p class="text-gray-600 dark:text-gray-400">{{ $client->company_name }}</p>
                    @endif
                </div>
            </div>
        </div>
        @if(auth()->user()->hasAdminAccess())
        <div class="flex gap-2">
            <a href="{{ route('kanban.clients.edit', $client) }}" class="inline-flex items-center gap-2 px-4 py-2 bg-gray-100 hover:bg-gray-200 dark:bg-gray-800 dark:hover:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-lg transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                </svg>
                Edit
            </a>
            <a href="{{ route('kanban.projects.create', ['client_id' => $client->id]) }}" class="inline-flex items-center gap-2 px-4 py-2 bg-brand-500 hover:bg-brand-600 text-white rounded-lg transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Proyek Baru
            </a>
        </div>
        @endif
    </div>

    {{-- Alert Messages --}}
    @if(session('success'))
    <div class="p-4 rounded-lg bg-green-50 border border-green-200 dark:bg-green-900/20 dark:border-green-800">
        <p class="text-green-700 dark:text-green-400">{{ session('success') }}</p>
    </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Client Info --}}
        <div class="lg:col-span-1">
            <div class="bg-white dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-800 p-6">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Informasi Kontak</h2>
                <dl class="space-y-4">
                    @if($client->email)
                    <div>
                        <dt class="text-xs text-gray-500 dark:text-gray-400 uppercase">Email</dt>
                        <dd class="mt-1 flex items-center gap-2 text-gray-900 dark:text-white">
                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                            </svg>
                            <a href="mailto:{{ $client->email }}" class="hover:text-brand-600 dark:hover:text-brand-400">{{ $client->email }}</a>
                        </dd>
                    </div>
                    @endif
                    @if($client->phone)
                    <div>
                        <dt class="text-xs text-gray-500 dark:text-gray-400 uppercase">Telepon</dt>
                        <dd class="mt-1 flex items-center gap-2 text-gray-900 dark:text-white">
                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                            </svg>
                            <a href="tel:{{ $client->phone }}" class="hover:text-brand-600 dark:hover:text-brand-400">{{ $client->phone }}</a>
                        </dd>
                    </div>
                    @endif
                    @if($client->address)
                    <div>
                        <dt class="text-xs text-gray-500 dark:text-gray-400 uppercase">Alamat</dt>
                        <dd class="mt-1 flex items-start gap-2 text-gray-900 dark:text-white">
                            <svg class="w-4 h-4 text-gray-400 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                            <span>{{ $client->address }}</span>
                        </dd>
                    </div>
                    @endif
                    <div>
                        <dt class="text-xs text-gray-500 dark:text-gray-400 uppercase">Terdaftar</dt>
                        <dd class="mt-1 text-gray-900 dark:text-white">{{ $client->created_at->format('d F Y') }}</dd>
                    </div>
                </dl>
            </div>
        </div>

        {{-- Projects --}}
        <div class="lg:col-span-2">
            <div class="bg-white dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-800">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-800 flex items-center justify-between">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Proyek ({{ $client->projects->count() }})</h2>
                </div>
                <div class="divide-y divide-gray-200 dark:divide-gray-800">
                    @forelse($client->projects as $project)
                    <a href="{{ route('kanban.projects.show', $project) }}" class="block p-4 hover:bg-gray-50 dark:hover:bg-gray-800/50 transition">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="font-medium text-gray-900 dark:text-white">{{ $project->name }}</p>
                                <p class="text-sm text-gray-500 dark:text-gray-400">{{ $project->project_code }}</p>
                            </div>
                            <div class="text-right">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                    {{ $project->status === 'active' ? 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400' : '' }}
                                    {{ $project->status === 'completed' ? 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400' : '' }}
                                    {{ $project->status === 'on_hold' ? 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-400' : '' }}
                                    {{ $project->status === 'cancelled' ? 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400' : '' }}
                                ">
                                    {{ ucfirst(str_replace('_', ' ', $project->status)) }}
                                </span>
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ $project->assets_count ?? $project->assets->count() }} asset</p>
                            </div>
                        </div>
                    </a>
                    @empty
                    <div class="p-8 text-center">
                        <svg class="w-12 h-12 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        <p class="text-gray-500 dark:text-gray-400">Belum ada proyek untuk klien ini</p>
                        @if(auth()->user()->hasAdminAccess())
                        <a href="{{ route('kanban.projects.create', ['client_id' => $client->id]) }}" class="mt-2 inline-block text-brand-600 hover:text-brand-700 dark:text-brand-400 font-medium">
                            Buat proyek pertama →
                        </a>
                        @endif
                    </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

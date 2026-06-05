@extends('layouts.app')

@section('title', 'Detail Klien - ' . $client->name)

@section('content')
<div class="space-y-6">
    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <a href="{{ route('kanban.clients.perusahaan') }}" class="inline-flex items-center text-sm text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300 mb-2">
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
        <div class="flex gap-2">
            <a href="{{ route('kanban.clients.edit', $client) }}" class="inline-flex items-center gap-2 px-4 py-2 bg-gray-100 hover:bg-gray-200 dark:bg-gray-800 dark:hover:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-lg transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                </svg>
                Edit
            </a>
            <a href="{{ route('kanban.assets.create', ['client_id' => $client->id]) }}" class="inline-flex items-center gap-2 px-4 py-2 bg-brand-500 hover:bg-brand-600 text-white rounded-lg transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Asset Baru
            </a>
        </div>
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
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Informasi Klien</h2>
                <dl class="space-y-4">
                    <div>
                        <dt class="text-xs text-gray-500 dark:text-gray-400 uppercase">Tipe</dt>
                        <dd class="mt-1">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                {{ $client->type === 'bank' ? 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400' : '' }}
                                {{ $client->type === 'pt_cv' ? 'bg-purple-100 text-purple-800 dark:bg-purple-900/30 dark:text-purple-400' : '' }}
                                {{ $client->type === 'debitur' ? 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400' : '' }}
                            ">
                                {{ $client->type === 'bank' ? 'Bank' : ($client->type === 'pt_cv' ? 'PT/CV' : 'Debitur') }}
                            </span>
                        </dd>
                    </div>
                    @if($client->company_name)
                    <div>
                        <dt class="text-xs text-gray-500 dark:text-gray-400 uppercase">Perusahaan</dt>
                        <dd class="mt-1 text-gray-900 dark:text-white">{{ $client->company_name }}</dd>
                    </div>
                    @endif
                    @if($client->spk_number)
                    <div>
                        <dt class="text-xs text-gray-500 dark:text-gray-400 uppercase">No SPK</dt>
                        <dd class="mt-1 text-gray-900 dark:text-white font-medium">{{ $client->spk_number }}</dd>
                    </div>
                    @endif
                    @if($client->parent)
                    <div>
                        <dt class="text-xs text-gray-500 dark:text-gray-400 uppercase">
                            {{ $client->type === 'debitur' ? 'Bank' : 'Perusahaan Induk' }}
                        </dt>
                        <dd class="mt-1">
                            <a href="{{ route('kanban.clients.show', $client->parent) }}" class="text-brand-600 dark:text-brand-400 hover:underline">
                                {{ $client->parent->display_name }}
                            </a>
                        </dd>
                    </div>
                    @endif
                    <div>
                        <dt class="text-xs text-gray-500 dark:text-gray-400 uppercase">Terdaftar</dt>
                        <dd class="mt-1 text-gray-900 dark:text-white">{{ $client->created_at->format('d F Y') }}</dd>
                    </div>
                </dl>
            </div>

            {{-- Children (Debitur/PT Anak) --}}
            @if($client->children->count() > 0)
            <div class="bg-white dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-800 mt-6">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-800">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white">
                        {{ $client->type === 'bank' ? 'Debitur' : 'Perusahaan Anak' }} ({{ $client->children->count() }})
                    </h2>
                </div>
                <div class="divide-y divide-gray-200 dark:divide-gray-800 max-h-64 overflow-y-auto">
                    @foreach($client->children as $child)
                    <a href="{{ route('kanban.clients.show', $child) }}" class="block p-4 hover:bg-gray-50 dark:hover:bg-gray-800/50 transition">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="font-medium text-gray-900 dark:text-white">{{ $child->name }}</p>
                                @if($child->company_name)
                                <p class="text-xs text-gray-500 dark:text-gray-400">{{ $child->company_name }}</p>
                                @endif
                            </div>
                            <span class="text-xs text-gray-500 dark:text-gray-400">{{ $child->assets_count ?? 0 }} asset</span>
                        </div>
                    </a>
                    @endforeach
                </div>
            </div>
            @endif
        </div>

        {{-- Assets --}}
        <div class="lg:col-span-2">
            <div class="bg-white dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-800">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-800 flex items-center justify-between">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Asset ({{ $client->assets->count() }})</h2>
                </div>
                <div class="divide-y divide-gray-200 dark:divide-gray-800">
                    @forelse($client->assets as $asset)
                    <a href="{{ route('kanban.assets.show', $asset) }}" class="block p-4 hover:bg-gray-50 dark:hover:bg-gray-800/50 transition">
                        <div class="flex items-center justify-between">
                            <div class="flex-1 min-w-0">
                                <p class="font-medium text-gray-900 dark:text-white truncate">{{ $asset->name }}</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">{{ $asset->asset_type_label }} • {{ $asset->location ?: 'Lokasi belum diisi' }}</p>
                            </div>
                            <div class="text-right ml-4">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800 dark:bg-gray-800 dark:text-gray-400">
                                    Stage {{ $asset->current_stage }}
                                </span>
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ $asset->stage_label }}</p>
                            </div>
                        </div>
                    </a>
                    @empty
                    <div class="p-8 text-center">
                        <svg class="w-12 h-12 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                        </svg>
                        <p class="text-gray-500 dark:text-gray-400">Belum ada asset untuk klien ini</p>
                        <a href="{{ route('kanban.assets.create', ['client_id' => $client->id]) }}" class="mt-2 inline-block text-brand-600 hover:text-brand-700 dark:text-brand-400 font-medium">
                            Buat asset pertama →
                        </a>
                    </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

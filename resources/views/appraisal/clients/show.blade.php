@extends('layouts.app')

@section('content')
<div class="max-w-6xl mx-auto">
    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4 mb-6">
        <div>
            <nav class="flex items-center gap-2 text-sm text-gray-500 dark:text-gray-400 mb-2">
                <a href="{{ route('appraisal.clients.index') }}" class="hover:text-gray-700 dark:hover:text-gray-300">Klien</a>
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
                <span class="text-gray-700 dark:text-gray-300">{{ $client->name }}</span>
            </nav>
            
            <div class="flex items-center gap-3">
                <div class="w-14 h-14 rounded-full bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center">
                    <span class="text-xl font-bold text-blue-600 dark:text-blue-400">
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

        <div class="flex items-center gap-2">
            <a href="{{ route('appraisal.projects.create', ['client_id' => $client->id]) }}" class="btn btn-primary btn-sm gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Buat Proyek
            </a>
            <a href="{{ route('appraisal.clients.edit', $client) }}" class="btn btn-outline btn-sm gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                </svg>
                Edit
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Main Content --}}
        <div class="lg:col-span-2 space-y-6">
            {{-- Stats --}}
            <div class="grid grid-cols-2 gap-4">
                <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-4">
                    <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">Total Proyek</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $client->projects->count() }}</p>
                </div>
                <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-4">
                    <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">Proyek Aktif</p>
                    <p class="text-2xl font-bold text-blue-600 dark:text-blue-400">
                        {{ $client->projects->where('current_stage', '!=', 'done')->count() }}
                    </p>
                </div>
            </div>

            {{-- Projects --}}
            <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Daftar Proyek</h3>
                </div>

                @if($client->projects && $client->projects->count() > 0)
                <div class="space-y-3">
                    @foreach($client->projects as $project)
                    <div class="flex items-center justify-between p-3 rounded-lg border border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700/50">
                        <div class="flex-1">
                            <a href="{{ route('appraisal.projects.show', $project) }}" class="font-medium text-gray-900 dark:text-white hover:text-blue-600 dark:hover:text-blue-400">
                                {{ $project->name }}
                            </a>
                            <div class="flex items-center gap-3 mt-1 text-xs text-gray-500 dark:text-gray-400">
                                <span>{{ $project->project_code }}</span>
                                <span>•</span>
                                <span>{{ Str::limit($project->location, 30) }}</span>
                            </div>
                        </div>
                        <div class="text-right">
                            @php
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
                                $stageLabels = \App\Models\ProjectKanban::STAGES;
                            @endphp
                            <span class="badge {{ $stageColors[$project->current_stage] ?? 'badge-ghost' }}">
                                {{ $stageLabels[$project->current_stage] ?? $project->current_stage }}
                            </span>
                        </div>
                    </div>
                    @endforeach
                </div>
                @else
                <div class="text-center py-8">
                    <svg class="w-12 h-12 mx-auto text-gray-300 dark:text-gray-600 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    <p class="text-gray-500 dark:text-gray-400 mb-4">Belum ada proyek</p>
                    <a href="{{ route('appraisal.projects.create', ['client_id' => $client->id]) }}" class="btn btn-primary btn-sm">
                        Buat Proyek Pertama
                    </a>
                </div>
                @endif
            </div>
        </div>

        {{-- Sidebar --}}
        <div class="space-y-6">
            {{-- Contact Info --}}
            <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Informasi Kontak</h3>
                
                <div class="space-y-4">
                    <div class="flex items-start gap-3">
                        <svg class="w-5 h-5 text-gray-400 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                        </svg>
                        <div>
                            <p class="text-xs text-gray-500 dark:text-gray-400">Email</p>
                            <a href="mailto:{{ $client->email }}" class="text-blue-600 dark:text-blue-400 hover:underline">
                                {{ $client->email }}
                            </a>
                        </div>
                    </div>

                    <div class="flex items-start gap-3">
                        <svg class="w-5 h-5 text-gray-400 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                        </svg>
                        <div>
                            <p class="text-xs text-gray-500 dark:text-gray-400">Telepon</p>
                            <a href="tel:{{ $client->phone }}" class="text-gray-900 dark:text-white hover:text-blue-600 dark:hover:text-blue-400">
                                {{ $client->phone }}
                            </a>
                        </div>
                    </div>

                    <div class="flex items-start gap-3">
                        <svg class="w-5 h-5 text-gray-400 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                        <div>
                            <p class="text-xs text-gray-500 dark:text-gray-400">Alamat</p>
                            <p class="text-gray-900 dark:text-white">{{ $client->address ?? '-' }}</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Created Info --}}
            <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Info Lainnya</h3>
                
                <div class="space-y-3 text-sm">
                    <div class="flex justify-between">
                        <span class="text-gray-500 dark:text-gray-400">Dibuat</span>
                        <span class="text-gray-900 dark:text-white">{{ $client->created_at?->format('d M Y') ?? '-' }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500 dark:text-gray-400">Diupdate</span>
                        <span class="text-gray-900 dark:text-white">{{ $client->updated_at?->format('d M Y') ?? '-' }}</span>
                    </div>
                </div>
            </div>

            {{-- Delete Action --}}
            @if($client->projects->count() === 0)
            <form action="{{ route('appraisal.clients.destroy', $client) }}" method="POST" 
                  onsubmit="return confirm('Yakin ingin menghapus klien ini?')">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-error btn-outline btn-block gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>
                    Hapus Klien
                </button>
            </form>
            @endif
        </div>
    </div>
</div>
@endsection

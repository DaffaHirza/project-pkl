@extends('layouts.app')

@section('title', 'Ringkasan Tugas')

@section('content')
<div class="space-y-6">
    {{-- Header --}}
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Ringkasan Tugas</h1>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Overview sistem</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('kanban.assets.index') }}" 
               class="inline-flex items-center gap-2 rounded-lg bg-gray-100 hover:bg-gray-200 dark:bg-gray-800 dark:hover:bg-gray-700 px-4 py-2.5 text-sm font-medium text-gray-700 dark:text-gray-300 transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17V7m0 10a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2h2a2 2 0 012 2m0 10a2 2 0 002 2h2a2 2 0 002-2M9 7a2 2 0 012-2h2a2 2 0 012 2m0 10V7m0 10a2 2 0 002 2h2a2 2 0 002-2V7a2 2 0 00-2-2h-2a2 2 0 00-2 2" />
                </svg>
                Kanban Board
            </a>
            <a href="{{ route('kanban.assets.create') }}" 
               class="inline-flex items-center gap-2 rounded-lg bg-brand-500 hover:bg-brand-600 px-4 py-2.5 text-sm font-medium text-white transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Asset Baru
            </a>
        </div>
    </div>

    {{-- Stats Cards --}}
    <div class="grid grid-cols-2 gap-4">
        {{-- Total Assets --}}
        <div class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-brand-500 to-brand-600 p-5 text-white">
            <div class="relative z-40">
                <p class="text-sm text-brand-100">Total Asset</p>
                <p class="text-3xl font-bold mt-1">{{ $stats['total_assets'] }}</p>
            </div>
            <div class="absolute -right-4 -bottom-4 opacity-20">
                <svg class="w-24 h-24" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                </svg>
            </div>
        </div>

        {{-- Total Clients --}}
        <div class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-purple-500 to-purple-600 p-5 text-white">
            <div class="relative z-40">
                <p class="text-sm text-purple-100">Total Klien</p>
                <p class="text-3xl font-bold mt-1">{{ $stats['total_clients'] }}</p>
            </div>
            <div class="absolute -right-4 -bottom-4 opacity-20">
                <svg class="w-24 h-24" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                </svg>
            </div>
        </div>
    </div>

    {{-- Charts Section --}}
    <div class="grid grid-cols-1 gap-6">
        {{-- Stage Distribution Chart --}}
        <div class="rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-gray-900">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Distribusi Stage</h3>
            <div class="relative h-64">
                <canvas id="stageChart"></canvas>
            </div>
        </div>
    </div>

    {{-- Bottom Section --}}
    <div class="grid grid-cols-1 gap-6">
        {{-- Recent Activities --}}
        <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900 overflow-hidden">
            <div class="px-5 py-4 border-b border-gray-200 dark:border-gray-800">
                <h3 class="font-semibold text-gray-900 dark:text-white">Aktivitas Terbaru</h3>
            </div>
            <div class="divide-y divide-gray-100 dark:divide-gray-800 max-h-72 overflow-y-auto">
                @forelse($recentActivities ?? [] as $activity)
                <div class="p-4">
                    <p class="text-sm text-gray-900 dark:text-white">{{ $activity->content }}</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ $activity->user->name ?? 'System' }} &middot; {{ $activity->created_at->diffForHumans() }}</p>
                </div>
                @empty
                <div class="p-6 text-center">
                    <div class="w-12 h-12 mx-auto mb-3 rounded-full bg-gray-100 dark:bg-gray-800 flex items-center justify-center">
                        <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Belum ada aktivitas</p>
                </div>
                @endforelse
            </div>
        </div>
    </div>

    {{-- Quick Actions --}}
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
        <a href="{{ route('kanban.clients.index') }}" class="group flex flex-col items-center p-5 rounded-2xl border border-gray-200 bg-white hover:border-purple-300 hover:shadow-lg transition dark:border-gray-800 dark:bg-gray-900 dark:hover:border-purple-700">
            <div class="w-12 h-12 rounded-xl bg-purple-100 dark:bg-purple-900/30 flex items-center justify-center mb-3 group-hover:scale-110 transition">
                <svg class="w-6 h-6 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                </svg>
            </div>
            <span class="text-sm font-medium text-gray-900 dark:text-white">Klien</span>
        </a>
        <a href="{{ route('kanban.assets.index') }}" class="group flex flex-col items-center p-5 rounded-2xl border border-gray-200 bg-white hover:border-blue-300 hover:shadow-lg transition dark:border-gray-800 dark:bg-gray-900 dark:hover:border-blue-700">
            <div class="w-12 h-12 rounded-xl bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center mb-3 group-hover:scale-110 transition">
                <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17V7m0 10a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2h2a2 2 0 012 2m0 10a2 2 0 002 2h2a2 2 0 002-2M9 7a2 2 0 012-2h2a2 2 0 012 2m0 10V7m0 10a2 2 0 002 2h2a2 2 0 002-2V7a2 2 0 00-2-2h-2a2 2 0 00-2 2" />
                </svg>
            </div>
            <span class="text-sm font-medium text-gray-900 dark:text-white">Kanban</span>
        </a>
        <a href="{{ route('kanban.recapitulations.index') }}" class="group flex flex-col items-center p-5 rounded-2xl border border-gray-200 bg-white hover:border-emerald-300 hover:shadow-lg transition dark:border-gray-800 dark:bg-gray-900 dark:hover:border-emerald-700">
            <div class="w-12 h-12 rounded-xl bg-emerald-100 dark:bg-emerald-900/30 flex items-center justify-center mb-3 group-hover:scale-110 transition">
                <svg class="w-6 h-6 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
            </div>
            <span class="text-sm font-medium text-gray-900 dark:text-white">Rekapitulasi</span>
        </a>
        <a href="{{ route('kanban.activity-log') }}" class="group flex flex-col items-center p-5 rounded-2xl border border-gray-200 bg-white hover:border-orange-300 hover:shadow-lg transition dark:border-gray-800 dark:bg-gray-900 dark:hover:border-orange-700">
            <div class="w-12 h-12 rounded-xl bg-orange-100 dark:bg-orange-900/30 flex items-center justify-center mb-3 group-hover:scale-110 transition">
                <svg class="w-6 h-6 text-orange-600 dark:text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
            <span class="text-sm font-medium text-gray-900 dark:text-white">Log</span>
        </a>
    </div>
</div>

{{-- Chart.js CDN --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Get stage data from PHP
    const stageData = @json($stats['assets_by_stage'] ?? []);
    const stages = @json(\App\Models\AssetKanban::STAGES);
    
    // Prepare labels and data for stage chart
    const stageLabels = Object.values(stages).map((name, i) => `${i + 1}. ${name.substring(0, 15)}...`);
    const stageValues = Object.keys(stages).map(key => stageData[key] || 0);
    
    // Stage Distribution Chart (Bar)
    const stageCtx = document.getElementById('stageChart').getContext('2d');
    new Chart(stageCtx, {
        type: 'bar',
        data: {
            labels: stageLabels,
            datasets: [{
                label: 'Jumlah Asset',
                data: stageValues,
                backgroundColor: [
                    '#1E3A8A', '#1D4ED8', '#2563EB', '#3B82F6', '#0EA5E9',
                    '#10B981', '#22C55E', '#84CC16', '#EAB308',
                    '#F59E0B', '#F97316', '#EF4444', '#A855F7'
                ],
                borderRadius: 6,
                borderSkipped: false,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: { stepSize: 1 },
                    grid: { color: 'rgba(0,0,0,0.05)' }
                },
                x: {
                    ticks: { 
                        maxRotation: 45,
                        minRotation: 45,
                        font: { size: 10 }
                    },
                    grid: { display: false }
                }
            }
        }
    });
});
</script>
@endsection

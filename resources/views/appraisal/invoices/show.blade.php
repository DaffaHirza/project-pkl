@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto">
    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div class="flex items-center gap-4">
            <a href="{{ route('appraisal.invoices.index') }}" class="btn btn-ghost btn-sm btn-square">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
            </a>
            <div>
                <div class="flex items-center gap-2">
                    <h1 class="text-2xl font-bold text-gray-900 dark:text-white">{{ $invoice->invoice_number }}</h1>
                    @php
                        $statusColors = [
                            'unpaid' => 'badge-warning',
                            'paid' => 'badge-success',
                            'cancelled' => 'badge-error',
                        ];
                        $statusLabels = [
                            'unpaid' => 'Belum Dibayar',
                            'paid' => 'Lunas',
                            'cancelled' => 'Dibatalkan',
                        ];
                    @endphp
                    <span class="badge {{ $statusColors[$invoice->status] ?? 'badge-ghost' }}">
                        {{ $statusLabels[$invoice->status] ?? $invoice->status }}
                    </span>
                </div>
                <p class="text-gray-600 dark:text-gray-400 mt-1">
                    {{ $invoice->project->project_code ?? '-' }} - {{ $invoice->project->client->name ?? '-' }}
                </p>
            </div>
        </div>
        <div class="flex items-center gap-2">
            @if($invoice->status === 'unpaid')
            <form action="{{ route('appraisal.invoices.markAsPaid', $invoice) }}" method="POST" class="inline">
                @csrf
                <button type="submit" class="btn btn-success btn-sm gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    Tandai Lunas
                </button>
            </form>
            @endif
            <a href="{{ route('appraisal.invoices.edit', $invoice) }}" class="btn btn-outline btn-sm gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                </svg>
                Edit
            </a>
        </div>
    </div>

    {{-- Invoice Preview --}}
    <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-8 mb-6">
        {{-- Header Section --}}
        <div class="flex flex-col md:flex-row md:justify-between gap-6 mb-8 pb-6 border-b border-gray-200 dark:border-gray-700">
            <div>
                <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-2">INVOICE</h2>
                <p class="text-gray-600 dark:text-gray-400">{{ $invoice->invoice_number }}</p>
            </div>
            <div class="text-right">
                <p class="font-bold text-gray-900 dark:text-white">{{ config('app.company_name', 'KJPP Nama Perusahaan') }}</p>
                <p class="text-sm text-gray-600 dark:text-gray-400">{{ config('app.company_address', 'Alamat Perusahaan') }}</p>
            </div>
        </div>

        {{-- Billing Info --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
            <div>
                <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-2">Ditagihkan Kepada</p>
                <p class="font-bold text-gray-900 dark:text-white">{{ $invoice->project->client->name ?? '-' }}</p>
                @if($invoice->project->client->company_name)
                <p class="text-sm text-gray-600 dark:text-gray-400">{{ $invoice->project->client->company_name }}</p>
                @endif
                <p class="text-sm text-gray-600 dark:text-gray-400">{{ $invoice->project->client->address ?? '' }}</p>
                <p class="text-sm text-gray-600 dark:text-gray-400">{{ $invoice->project->client->email ?? '' }}</p>
                <p class="text-sm text-gray-600 dark:text-gray-400">{{ $invoice->project->client->phone ?? '' }}</p>
            </div>
            <div class="md:text-right">
                <div class="space-y-2">
                    <div class="flex justify-between md:justify-end md:gap-8">
                        <span class="text-sm text-gray-500 dark:text-gray-400">Tanggal Jatuh Tempo:</span>
                        <span class="text-sm font-medium {{ $invoice->payment_due_date?->isPast() && $invoice->status !== 'paid' ? 'text-red-600' : 'text-gray-900 dark:text-white' }}">
                            {{ $invoice->payment_due_date?->format('d M Y') ?? '-' }}
                        </span>
                    </div>
                    
                    <div class="flex justify-between md:justify-end md:gap-8">
                        <span class="text-sm text-gray-500 dark:text-gray-400">Proyek:</span>
                        <span class="text-sm font-medium text-gray-900 dark:text-white">{{ $invoice->project->project_code ?? '-' }}</span>
                    </div>

                    @if($invoice->paid_at)
                    <div class="flex justify-between md:justify-end md:gap-8">
                        <span class="text-sm text-gray-500 dark:text-gray-400">Tanggal Bayar:</span>
                        <span class="text-sm font-medium text-green-600">{{ $invoice->paid_at->format('d M Y') }}</span>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Project Details --}}
        <div class="mb-8">
            <h3 class="text-sm font-semibold text-gray-900 dark:text-white mb-4">Detail Proyek</h3>
            <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                    <div>
                        <span class="text-gray-500 dark:text-gray-400">Nama Proyek:</span>
                        <p class="font-medium text-gray-900 dark:text-white">{{ $invoice->project->name ?? '-' }}</p>
                    </div>
                    <div>
                        <span class="text-gray-500 dark:text-gray-400">Lokasi:</span>
                        <p class="font-medium text-gray-900 dark:text-white">{{ $invoice->project->location ?? '-' }}</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Status Info --}}
        @if($invoice->status === 'unpaid' && $invoice->payment_due_date?->isPast())
        <div class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg p-4 mb-6">
            <div class="flex items-center gap-3">
                <svg class="w-5 h-5 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <div>
                    <p class="font-medium text-red-900 dark:text-red-100">Invoice sudah jatuh tempo!</p>
                    <p class="text-sm text-red-700 dark:text-red-300">
                        Jatuh tempo: {{ $invoice->payment_due_date->format('d M Y') }} ({{ $invoice->payment_due_date->diffForHumans() }})
                    </p>
                </div>
            </div>
        </div>
        @elseif($invoice->status === 'paid')
        <div class="bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg p-4 mb-6">
            <div class="flex items-center gap-3">
                <svg class="w-5 h-5 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <div>
                    <p class="font-medium text-green-900 dark:text-green-100">Invoice sudah lunas</p>
                    @if($invoice->paid_at)
                    <p class="text-sm text-green-700 dark:text-green-300">
                        Dibayar pada: {{ $invoice->paid_at->format('d M Y') }}
                    </p>
                    @endif
                </div>
            </div>
        </div>
        @endif
    </div>

    {{-- Actions --}}
    <div class="flex items-center justify-between">
        <a href="{{ route('appraisal.projects.show', $invoice->project) }}" class="btn btn-ghost gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
            </svg>
            Kembali ke Proyek
        </a>

        @if($invoice->status !== 'paid')
        <form action="{{ route('appraisal.invoices.destroy', $invoice) }}" method="POST" 
              onsubmit="return confirm('Yakin ingin menghapus invoice ini?')">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-error btn-outline btn-sm gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                </svg>
                Hapus
            </button>
        </form>
        @endif
    </div>
</div>
@endsection

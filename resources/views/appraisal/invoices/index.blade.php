@extends('layouts.app')

@section('content')
<div>
    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Daftar Invoice</h1>
            <p class="text-gray-600 dark:text-gray-400 mt-1">Kelola tagihan dan pembayaran</p>
        </div>
        <div class="dropdown dropdown-end">
            <label tabindex="0" class="btn btn-primary gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Buat Invoice
            </label>
            <ul tabindex="0" class="dropdown-content z-[1] menu p-2 shadow bg-base-100 rounded-box w-72 max-h-96 overflow-y-auto">
                <li class="menu-title">Pilih Proyek</li>
                @forelse($projects ?? [] as $project)
                <li>
                    <a href="{{ route('appraisal.invoices.create', $project) }}">
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
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-4">
            <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">Total Invoice</p>
            <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $invoices->total() }}</p>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-4">
            <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">Belum Dibayar</p>
            <p class="text-2xl font-bold text-yellow-600 dark:text-yellow-400">{{ $invoices->where('status', 'unpaid')->count() }}</p>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-4">
            <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">Sudah Dibayar</p>
            <p class="text-2xl font-bold text-green-600 dark:text-green-400">{{ $invoices->where('status', 'paid')->count() }}</p>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-4">
            <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">Dibatalkan</p>
            <p class="text-2xl font-bold text-red-600 dark:text-red-400">{{ $invoices->where('status', 'cancelled')->count() }}</p>
        </div>
    </div>

    {{-- Filters --}}
    <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-4 mb-6">
        <form action="{{ route('appraisal.invoices.index') }}" method="GET" class="flex flex-col lg:flex-row gap-4">
            <div class="flex-1">
                <input type="text" name="search" value="{{ request('search') }}" 
                       placeholder="Cari nomor invoice atau proyek..." 
                       class="input input-bordered w-full">
            </div>
            <select name="status" class="select select-bordered w-full lg:w-40">
                <option value="">Semua Status</option>
                <option value="unpaid" {{ request('status') === 'unpaid' ? 'selected' : '' }}>Belum Dibayar</option>
                <option value="paid" {{ request('status') === 'paid' ? 'selected' : '' }}>Lunas</option>
                <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>Dibatalkan</option>
            </select>
            <button type="submit" class="btn btn-primary">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
            </button>
            @if(request()->hasAny(['search', 'status']))
            <a href="{{ route('appraisal.invoices.index') }}" class="btn btn-ghost">Reset</a>
            @endif
        </form>
    </div>

    {{-- Overdue Alert --}}
    @php
        $overdueInvoices = $invoices->filter(function($invoice) {
            return $invoice->status === 'unpaid' && $invoice->payment_due_date && $invoice->payment_due_date->isPast();
        });
    @endphp
    @if($overdueInvoices->count() > 0)
    <div class="alert alert-warning mb-6">
        <svg class="w-6 h-6 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
        </svg>
        <div>
            <h3 class="font-bold">Invoice Jatuh Tempo</h3>
            <div class="text-sm">{{ $overdueInvoices->count() }} invoice sudah melewati tanggal jatuh tempo</div>
        </div>
    </div>
    @endif

    {{-- Invoices Table --}}
    <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="table w-full">
                <thead>
                    <tr>
                        <th>No. Invoice</th>
                        <th>Proyek</th>
                        <th>Jatuh Tempo</th>
                        <th>Tanggal Bayar</th>
                        <th>Status</th>
                        <th class="text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($invoices as $invoice)
                    @php
                        $isOverdue = $invoice->status === 'unpaid' && $invoice->payment_due_date && $invoice->payment_due_date->isPast();
                    @endphp
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 {{ $isOverdue ? 'bg-red-50 dark:bg-red-900/10' : '' }}">
                        <td>
                            <span class="font-mono text-sm font-medium text-gray-900 dark:text-white">
                                {{ $invoice->invoice_number }}
                            </span>
                        </td>
                        <td>
                            @if($invoice->project)
                            <div>
                                <a href="{{ route('appraisal.projects.show', $invoice->project) }}" class="text-sm font-medium text-gray-900 dark:text-white hover:text-blue-600 dark:hover:text-blue-400">
                                    {{ $invoice->project->project_code }}
                                </a>
                                <p class="text-xs text-gray-500 dark:text-gray-400">{{ Str::limit($invoice->project->name, 25) }}</p>
                            </div>
                            @else
                            <span class="text-gray-400">-</span>
                            @endif
                        </td>
                        <td>
                            @if($invoice->payment_due_date)
                            <span class="text-sm {{ $isOverdue ? 'text-red-600 dark:text-red-400 font-medium' : 'text-gray-600 dark:text-gray-400' }}">
                                {{ $invoice->payment_due_date->format('d M Y') }}
                                @if($isOverdue)
                                <span class="text-xs block">({{ $invoice->payment_due_date->diffForHumans() }})</span>
                                @endif
                            </span>
                            @else
                            <span class="text-gray-400">-</span>
                            @endif
                        </td>
                        <td>
                            @if($invoice->paid_at)
                            <span class="text-sm text-green-600 dark:text-green-400">
                                {{ $invoice->paid_at->format('d M Y') }}
                            </span>
                            @else
                            <span class="text-gray-400">-</span>
                            @endif
                        </td>
                        <td>
                            @if($invoice->status === 'paid')
                            <span class="badge badge-success badge-sm gap-1">
                                <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                </svg>
                                Lunas
                            </span>
                            @elseif($invoice->status === 'cancelled')
                            <span class="badge badge-ghost badge-sm">Dibatalkan</span>
                            @elseif($isOverdue)
                            <span class="badge badge-error badge-sm">Jatuh Tempo</span>
                            @else
                            <span class="badge badge-warning badge-sm">Belum Dibayar</span>
                            @endif
                        </td>
                        <td class="text-right">
                            <div class="dropdown dropdown-end">
                                <label tabindex="0" class="btn btn-ghost btn-sm btn-square">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z" />
                                    </svg>
                                </label>
                                <ul tabindex="0" class="dropdown-content z-[1] menu p-2 shadow bg-base-100 rounded-box w-52">
                                    <li><a href="{{ route('appraisal.invoices.show', $invoice) }}">Lihat Detail</a></li>
                                    <li><a href="{{ route('appraisal.invoices.edit', $invoice) }}">Edit</a></li>
                                    @if($invoice->status === 'unpaid')
                                    <li>
                                        <form action="{{ route('appraisal.invoices.mark-paid', $invoice) }}" method="POST">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="text-success w-full text-left">Tandai Lunas</button>
                                        </form>
                                    </li>
                                    @endif
                                    <li class="divider"></li>
                                    <li>
                                        <form action="{{ route('appraisal.invoices.destroy', $invoice) }}" method="POST" 
                                              onsubmit="return confirm('Apakah Anda yakin ingin menghapus invoice ini?')">
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
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 14l6-6m-5.5.5h.01m4.99 5h.01M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16l3.5-2 3.5 2 3.5-2 3.5 2zM10 8.5a.5.5 0 11-1 0 .5.5 0 011 0zm5 5a.5.5 0 11-1 0 .5.5 0 011 0z" />
                            </svg>
                            <p class="text-gray-500 dark:text-gray-400 mb-4">Belum ada invoice</p>
                            <a href="{{ route('appraisal.projects.index') }}" class="btn btn-primary btn-sm">Pilih Proyek untuk Invoice</a>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if($invoices->hasPages())
        <div class="px-4 py-3 border-t border-gray-200 dark:border-gray-700">
            {{ $invoices->links() }}
        </div>
        @endif
    </div>
</div>
@endsection

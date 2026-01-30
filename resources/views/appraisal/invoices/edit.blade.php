@extends('layouts.app')

@section('content')
<div class="max-w-2xl mx-auto">
    {{-- Header --}}
    <div class="flex items-center gap-4 mb-6">
        <a href="{{ route('appraisal.invoices.show', $invoice) }}" class="btn btn-ghost btn-sm btn-square">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
            </svg>
        </a>
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Edit Invoice</h1>
            <p class="text-gray-600 dark:text-gray-400 mt-1">{{ $invoice->invoice_number }}</p>
        </div>
    </div>

    {{-- Error Messages --}}
    @if($errors->any())
    <div class="alert alert-error mb-6">
        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
        </svg>
        <div>
            <h3 class="font-bold">Terdapat kesalahan:</h3>
            <ul class="list-disc list-inside text-sm">
                @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    </div>
    @endif

    {{-- Invoice Info Card --}}
    <div class="bg-gray-50 dark:bg-gray-800/50 rounded-lg border border-gray-200 dark:border-gray-700 p-4 mb-6">
        <div class="grid grid-cols-2 gap-4 text-sm">
            <div>
                <span class="text-gray-500 dark:text-gray-400">Nomor Invoice</span>
                <p class="font-medium text-gray-900 dark:text-white">{{ $invoice->invoice_number }}</p>
            </div>
            <div>
                <span class="text-gray-500 dark:text-gray-400">Proyek</span>
                <p class="font-medium text-gray-900 dark:text-white">{{ $invoice->project->name ?? '-' }}</p>
            </div>
            <div>
                <span class="text-gray-500 dark:text-gray-400">Klien</span>
                <p class="font-medium text-gray-900 dark:text-white">{{ $invoice->project->client->name ?? '-' }}</p>
            </div>
            <div>
                <span class="text-gray-500 dark:text-gray-400">Status Saat Ini</span>
                <p>
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
                </p>
            </div>
        </div>
    </div>

    <form action="{{ route('appraisal.invoices.update', $invoice) }}" method="POST">
        @csrf
        @method('PUT')

        {{-- Invoice Details --}}
        <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-6 mb-6">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Detail Invoice</h2>
            
            <div class="space-y-4">
                {{-- Payment Due Date --}}
                <div class="form-control">
                    <label class="label">
                        <span class="label-text font-medium">Tanggal Jatuh Tempo <span class="text-red-500">*</span></span>
                    </label>
                    <input type="date" name="payment_due_date" 
                           value="{{ old('payment_due_date', $invoice->payment_due_date?->format('Y-m-d')) }}" 
                           class="input input-bordered w-full @error('payment_due_date') input-error @enderror" required>
                    @error('payment_due_date')
                    <label class="label"><span class="label-text-alt text-error">{{ $message }}</span></label>
                    @enderror
                </div>

                {{-- Status --}}
                <div class="form-control">
                    <label class="label">
                        <span class="label-text font-medium">Status <span class="text-red-500">*</span></span>
                    </label>
                    <select name="status" class="select select-bordered w-full @error('status') select-error @enderror" required>
                        <option value="unpaid" {{ old('status', $invoice->status) == 'unpaid' ? 'selected' : '' }}>Belum Dibayar</option>
                        <option value="paid" {{ old('status', $invoice->status) == 'paid' ? 'selected' : '' }}>Lunas</option>
                        <option value="cancelled" {{ old('status', $invoice->status) == 'cancelled' ? 'selected' : '' }}>Dibatalkan</option>
                    </select>
                    @error('status')
                    <label class="label"><span class="label-text-alt text-error">{{ $message }}</span></label>
                    @enderror
                </div>
            </div>
        </div>

        {{-- Paid Info --}}
        @if($invoice->paid_at)
        <div class="bg-green-50 dark:bg-green-900/20 rounded-lg border border-green-200 dark:border-green-800 p-4 mb-6">
            <div class="flex items-center gap-3">
                <svg class="w-5 h-5 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <div>
                    <p class="font-medium text-green-900 dark:text-green-100">Invoice sudah dibayar</p>
                    <p class="text-sm text-green-700 dark:text-green-300">
                        Tanggal pembayaran: {{ $invoice->paid_at->format('d M Y') }}
                    </p>
                </div>
            </div>
        </div>
        @endif

        {{-- Actions --}}
        <div class="flex items-center justify-end gap-3">
            <a href="{{ route('appraisal.invoices.show', $invoice) }}" class="btn btn-ghost">Batal</a>
            <button type="submit" class="btn btn-primary gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
                Simpan Perubahan
            </button>
        </div>
    </form>
</div>
@endsection

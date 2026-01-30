@extends('layouts.app')

@section('content')
<div class="max-w-2xl mx-auto">
    {{-- Header --}}
    <div class="flex items-center gap-4 mb-6">
        <a href="{{ route('appraisal.projects.show', $project) }}" class="btn btn-ghost btn-sm btn-square">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
            </svg>
        </a>
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Buat Invoice Baru</h1>
            <p class="text-gray-600 dark:text-gray-400 mt-1">{{ $project->project_code }} - {{ $project->name }}</p>
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

    {{-- Project Info Card --}}
    <div class="bg-blue-50 dark:bg-blue-900/20 rounded-lg border border-blue-200 dark:border-blue-800 p-4 mb-6">
        <div class="flex items-start gap-3">
            <svg class="w-5 h-5 text-blue-600 dark:text-blue-400 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <div>
                <p class="font-medium text-blue-900 dark:text-blue-100">Informasi Proyek</p>
                <p class="text-sm text-blue-700 dark:text-blue-300 mt-1">
                    <strong>Klien:</strong> {{ $project->client->name ?? '-' }}<br>
                    <strong>Lokasi:</strong> {{ $project->location ?? '-' }}
                </p>
            </div>
        </div>
    </div>

    <form action="{{ route('appraisal.invoices.store', $project) }}" method="POST">
        @csrf

        {{-- Invoice Details --}}
        <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-6 mb-6">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Detail Invoice</h2>
            
            <div class="space-y-4">
                {{-- Invoice Number Info --}}
                <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-4">
                    <p class="text-sm text-gray-600 dark:text-gray-400">
                        <svg class="w-4 h-4 inline-block mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        Nomor invoice akan digenerate otomatis oleh sistem.
                    </p>
                </div>

                {{-- Payment Due Date --}}
                <div class="form-control">
                    <label class="label">
                        <span class="label-text font-medium">Tanggal Jatuh Tempo <span class="text-red-500">*</span></span>
                    </label>
                    <input type="date" name="payment_due_date" value="{{ old('payment_due_date', date('Y-m-d', strtotime('+30 days'))) }}" 
                           class="input input-bordered w-full @error('payment_due_date') input-error @enderror" 
                           min="{{ date('Y-m-d', strtotime('+1 day')) }}" required>
                    @error('payment_due_date')
                    <label class="label"><span class="label-text-alt text-error">{{ $message }}</span></label>
                    @enderror
                    <label class="label">
                        <span class="label-text-alt text-gray-500">Default: 30 hari dari hari ini</span>
                    </label>
                </div>
            </div>
        </div>

        {{-- Summary Info --}}
        <div class="bg-gray-50 dark:bg-gray-800/50 rounded-lg border border-gray-200 dark:border-gray-700 p-4 mb-6">
            <h3 class="font-medium text-gray-900 dark:text-white mb-3">Ringkasan</h3>
            <div class="space-y-2 text-sm">
                <div class="flex justify-between">
                    <span class="text-gray-600 dark:text-gray-400">Proyek</span>
                    <span class="font-medium text-gray-900 dark:text-white">{{ $project->name }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600 dark:text-gray-400">Klien</span>
                    <span class="font-medium text-gray-900 dark:text-white">{{ $project->client->name ?? '-' }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600 dark:text-gray-400">Status</span>
                    <span class="badge badge-warning">Belum Dibayar</span>
                </div>
            </div>
        </div>

        {{-- Actions --}}
        <div class="flex items-center justify-end gap-3">
            <a href="{{ route('appraisal.projects.show', $project) }}" class="btn btn-ghost">Batal</a>
            <button type="submit" class="btn btn-primary gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
                Buat Invoice
            </button>
        </div>
    </form>
</div>
@endsection

@extends('layouts.app')

@section('content')
<div class="max-w-2xl mx-auto">
    {{-- Header --}}
    <div class="mb-6">
        <nav class="flex items-center gap-2 text-sm text-gray-500 dark:text-gray-400 mb-2">
            <a href="{{ route('appraisal.projects.index') }}" class="hover:text-gray-700 dark:hover:text-gray-300">Proyek</a>
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
            </svg>
            <span class="text-gray-700 dark:text-gray-300">Buat Proyek Baru</span>
        </nav>
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Buat Proyek Penilaian Baru</h1>
        <p class="text-gray-600 dark:text-gray-400 mt-1">Isi informasi proyek untuk memulai proses penilaian</p>
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

    {{-- Form --}}
    <form action="{{ route('appraisal.projects.store') }}" method="POST" class="space-y-6">
        @csrf

        {{-- Client Selection --}}
        <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-6">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Informasi Klien</h3>
            
            <div class="form-control">
                <label class="label">
                    <span class="label-text font-medium">Klien <span class="text-red-500">*</span></span>
                </label>
                <div class="flex gap-2">
                    <select name="client_id" class="select select-bordered flex-1 @error('client_id') select-error @enderror" required>
                        <option value="">Pilih Klien</option>
                        @foreach($clients as $client)
                        <option value="{{ $client->id }}" {{ old('client_id', request('client_id')) == $client->id ? 'selected' : '' }}>
                            {{ $client->name }} {{ $client->company_name ? '- ' . $client->company_name : '' }}
                        </option>
                        @endforeach
                    </select>
                    <a href="{{ route('appraisal.clients.create') }}" class="btn btn-outline" title="Tambah Klien Baru">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                    </a>
                </div>
                @error('client_id')
                <label class="label"><span class="label-text-alt text-error">{{ $message }}</span></label>
                @enderror
            </div>
        </div>

        {{-- Project Info --}}
        <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-6">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Informasi Proyek</h3>
            
            <div class="space-y-4">
                {{-- Project Name --}}
                <div class="form-control">
                    <label class="label">
                        <span class="label-text font-medium">Nama Proyek <span class="text-red-500">*</span></span>
                    </label>
                    <input type="text" name="name" value="{{ old('name') }}" 
                           class="input input-bordered w-full @error('name') input-error @enderror" 
                           placeholder="Contoh: Penilaian Ruko Jl. Sudirman No. 123" required>
                    @error('name')
                    <label class="label"><span class="label-text-alt text-error">{{ $message }}</span></label>
                    @enderror
                    <label class="label">
                        <span class="label-text-alt text-gray-500">Kode proyek akan dibuat otomatis oleh sistem</span>
                    </label>
                </div>

                {{-- Location --}}
                <div class="form-control">
                    <label class="label">
                        <span class="label-text font-medium">Lokasi/Alamat Objek <span class="text-red-500">*</span></span>
                    </label>
                    <textarea name="location" rows="3" 
                              class="textarea textarea-bordered w-full @error('location') textarea-error @enderror" 
                              placeholder="Alamat lengkap objek penilaian, termasuk RT/RW, Kelurahan, Kecamatan, Kota" required>{{ old('location') }}</textarea>
                    @error('location')
                    <label class="label"><span class="label-text-alt text-error">{{ $message }}</span></label>
                    @enderror
                </div>

                {{-- Due Date --}}
                <div class="form-control">
                    <label class="label">
                        <span class="label-text font-medium">Target Selesai</span>
                    </label>
                    <input type="date" name="due_date" value="{{ old('due_date') }}" 
                           class="input input-bordered w-full @error('due_date') input-error @enderror"
                           min="{{ date('Y-m-d', strtotime('+1 day')) }}">
                    @error('due_date')
                    <label class="label"><span class="label-text-alt text-error">{{ $message }}</span></label>
                    @enderror
                    <label class="label">
                        <span class="label-text-alt text-gray-500">Opsional. Tanggal harus setelah hari ini.</span>
                    </label>
                </div>
            </div>
        </div>

        {{-- Info Box --}}
        <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-4">
            <div class="flex gap-3">
                <svg class="w-5 h-5 text-blue-600 dark:text-blue-400 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <div class="text-sm text-blue-700 dark:text-blue-300">
                    <p class="font-medium mb-1">Informasi:</p>
                    <ul class="list-disc list-inside space-y-1 text-blue-600 dark:text-blue-400">
                        <li>Proyek akan dimulai pada tahap <strong>Lead</strong></li>
                        <li>Kode proyek akan digenerate otomatis</li>
                        <li>Anda bisa menambahkan detail lain setelah proyek dibuat</li>
                    </ul>
                </div>
            </div>
        </div>

        {{-- Actions --}}
        <div class="flex items-center justify-end gap-3 pt-4">
            <a href="{{ route('appraisal.projects.index') }}" class="btn btn-ghost">Batal</a>
            <button type="submit" class="btn btn-primary gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
                Buat Proyek
            </button>
        </div>
    </form>
</div>
@endsection

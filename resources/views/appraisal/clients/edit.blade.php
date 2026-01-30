@extends('layouts.app')

@section('content')
<div class="max-w-2xl mx-auto">
    {{-- Header --}}
    <div class="mb-6">
        <nav class="flex items-center gap-2 text-sm text-gray-500 dark:text-gray-400 mb-2">
            <a href="{{ route('appraisal.clients.index') }}" class="hover:text-gray-700 dark:hover:text-gray-300">Klien</a>
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
            </svg>
            <a href="{{ route('appraisal.clients.show', $client) }}" class="hover:text-gray-700 dark:hover:text-gray-300">{{ $client->name }}</a>
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
            </svg>
            <span class="text-gray-700 dark:text-gray-300">Edit</span>
        </nav>
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Edit Klien</h1>
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
    <form action="{{ route('appraisal.clients.update', $client) }}" method="POST" class="space-y-6">
        @csrf
        @method('PUT')

        {{-- Basic Info --}}
        <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-6">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Informasi Dasar</h3>
            
            <div class="space-y-4">
                {{-- Name --}}
                <div class="form-control">
                    <label class="label">
                        <span class="label-text font-medium">Nama Lengkap / PIC <span class="text-red-500">*</span></span>
                    </label>
                    <input type="text" name="name" value="{{ old('name', $client->name) }}" 
                           class="input input-bordered w-full @error('name') input-error @enderror" 
                           placeholder="Nama lengkap atau PIC perusahaan" required>
                    @error('name')
                    <label class="label"><span class="label-text-alt text-error">{{ $message }}</span></label>
                    @enderror
                </div>

                {{-- Company Name --}}
                <div class="form-control">
                    <label class="label">
                        <span class="label-text font-medium">Nama Perusahaan/Instansi</span>
                    </label>
                    <input type="text" name="company_name" value="{{ old('company_name', $client->company_name) }}" 
                           class="input input-bordered w-full @error('company_name') input-error @enderror" 
                           placeholder="Nama perusahaan atau instansi (opsional)">
                    @error('company_name')
                    <label class="label"><span class="label-text-alt text-error">{{ $message }}</span></label>
                    @enderror
                </div>
            </div>
        </div>

        {{-- Contact Info --}}
        <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-6">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Informasi Kontak</h3>
            
            <div class="space-y-4">
                {{-- Email --}}
                <div class="form-control">
                    <label class="label">
                        <span class="label-text font-medium">Email <span class="text-red-500">*</span></span>
                    </label>
                    <input type="email" name="email" value="{{ old('email', $client->email) }}" 
                           class="input input-bordered w-full @error('email') input-error @enderror" 
                           placeholder="email@example.com" required>
                    @error('email')
                    <label class="label"><span class="label-text-alt text-error">{{ $message }}</span></label>
                    @enderror
                </div>

                {{-- Phone --}}
                <div class="form-control">
                    <label class="label">
                        <span class="label-text font-medium">Nomor Telepon <span class="text-red-500">*</span></span>
                    </label>
                    <input type="tel" name="phone" value="{{ old('phone', $client->phone) }}" 
                           class="input input-bordered w-full @error('phone') input-error @enderror" 
                           placeholder="08xxxxxxxxxx" required>
                    @error('phone')
                    <label class="label"><span class="label-text-alt text-error">{{ $message }}</span></label>
                    @enderror
                </div>
            </div>
        </div>

        {{-- Address --}}
        <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-6">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Alamat</h3>
            
            <div class="form-control">
                <label class="label">
                    <span class="label-text font-medium">Alamat Lengkap <span class="text-red-500">*</span></span>
                </label>
                <textarea name="address" rows="3" 
                          class="textarea textarea-bordered w-full @error('address') textarea-error @enderror" 
                          placeholder="Jalan, nomor, RT/RW, kelurahan, kecamatan, kota, provinsi" required>{{ old('address', $client->address) }}</textarea>
                @error('address')
                <label class="label"><span class="label-text-alt text-error">{{ $message }}</span></label>
                @enderror
            </div>
        </div>

        {{-- Actions --}}
        <div class="flex items-center justify-end gap-3">
            <a href="{{ route('appraisal.clients.show', $client) }}" class="btn btn-ghost">Batal</a>
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

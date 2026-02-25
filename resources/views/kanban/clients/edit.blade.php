@extends('layouts.app')

@section('title', 'Edit Klien - ' . $client->name)

@section('content')
<div class="max-w-2xl mx-auto">
    {{-- Header --}}
    <div class="mb-6">
        <a href="{{ route('kanban.clients.show', $client) }}" class="inline-flex items-center text-sm text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300 mb-2">
            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
            </svg>
            Kembali
        </a>
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Edit Klien</h1>
        <p class="text-gray-600 dark:text-gray-400 mt-1">{{ $client->name }}</p>
    </div>

    {{-- Form --}}
    <div class="bg-white dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-800 p-6">
        <form action="{{ route('kanban.clients.update', $client) }}" method="POST">
            @csrf
            @method('PUT')
            
            <div class="space-y-5">
                {{-- Name --}}
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        Nama Klien <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="name" id="name" value="{{ old('name', $client->name) }}" required
                           class="w-full px-4 py-2 rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-brand-500 focus:border-transparent @error('name') border-red-500 @enderror">
                    @error('name')
                    <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Company Name --}}
                <div>
                    <label for="company_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        Nama Perusahaan
                    </label>
                    <input type="text" name="company_name" id="company_name" value="{{ old('company_name', $client->company_name) }}"
                           class="w-full px-4 py-2 rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-brand-500 focus:border-transparent @error('company_name') border-red-500 @enderror">
                    @error('company_name')
                    <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Email --}}
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        Email
                    </label>
                    <input type="email" name="email" id="email" value="{{ old('email', $client->email) }}"
                           class="w-full px-4 py-2 rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-brand-500 focus:border-transparent @error('email') border-red-500 @enderror">
                    @error('email')
                    <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Phone --}}
                <div>
                    <label for="phone" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        No. Telepon
                    </label>
                    <input type="text" name="phone" id="phone" value="{{ old('phone', $client->phone) }}"
                           class="w-full px-4 py-2 rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-brand-500 focus:border-transparent @error('phone') border-red-500 @enderror">
                    @error('phone')
                    <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Address --}}
                <div>
                    <label for="address" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        Alamat
                    </label>
                    <textarea name="address" id="address" rows="3"
                              class="w-full px-4 py-2 rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-brand-500 focus:border-transparent @error('address') border-red-500 @enderror">{{ old('address', $client->address) }}</textarea>
                    @error('address')
                    <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            {{-- Actions --}}
            <div class="flex items-center justify-end gap-3 mt-6 pt-6 border-t border-gray-200 dark:border-gray-800">
                <a href="{{ route('kanban.clients.show', $client) }}" class="px-4 py-2 text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800 rounded-lg transition">
                    Batal
                </a>
                <button type="submit" class="px-6 py-2 bg-brand-500 hover:bg-brand-600 text-white rounded-lg font-medium transition">
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

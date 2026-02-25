@extends('layouts.app')

@section('title', 'Buat Proyek Baru')

@section('content')
<div class="max-w-2xl mx-auto">
    {{-- Header --}}
    <div class="mb-6">
        <a href="{{ route('kanban.projects.index') }}" class="inline-flex items-center text-sm text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300 mb-2">
            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
            </svg>
            Kembali
        </a>
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Buat Proyek Baru</h1>
        <p class="text-gray-600 dark:text-gray-400 mt-1">Isi data proyek dengan lengkap</p>
    </div>

    {{-- Form --}}
    <div class="bg-white dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-800 p-6">
        <form action="{{ route('kanban.projects.store') }}" method="POST">
            @csrf
            
            <div class="space-y-5">
                {{-- Client --}}
                <div>
                    <label for="client_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        Klien <span class="text-red-500">*</span>
                    </label>
                    <select name="client_id" id="client_id" required
                            class="w-full px-4 py-2 rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-brand-500 @error('client_id') border-red-500 @enderror">
                        <option value="">Pilih Klien</option>
                        @foreach($clients as $client)
                        <option value="{{ $client->id }}" {{ old('client_id', request('client_id')) == $client->id ? 'selected' : '' }}>
                            {{ $client->name }}{{ $client->company_name ? ' - ' . $client->company_name : '' }}
                        </option>
                        @endforeach
                    </select>
                    @error('client_id')
                    <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Name --}}
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        Nama Proyek <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="name" id="name" value="{{ old('name') }}" required
                           class="w-full px-4 py-2 rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-brand-500 @error('name') border-red-500 @enderror"
                           placeholder="Nama proyek penilaian">
                    @error('name')
                    <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Description --}}
                <div>
                    <label for="description" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        Deskripsi
                    </label>
                    <textarea name="description" id="description" rows="3"
                              class="w-full px-4 py-2 rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-brand-500 @error('description') border-red-500 @enderror"
                              placeholder="Deskripsi proyek (opsional)">{{ old('description') }}</textarea>
                    @error('description')
                    <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Due Date --}}
                <div>
                    <label for="due_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        Target Selesai
                    </label>
                    <input type="date" name="due_date" id="due_date" value="{{ old('due_date') }}"
                           class="w-full px-4 py-2 rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-brand-500 @error('due_date') border-red-500 @enderror">
                    @error('due_date')
                    <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Status --}}
                <div>
                    <label for="status" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        Status
                    </label>
                    <select name="status" id="status"
                            class="w-full px-4 py-2 rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-brand-500">
                        <option value="active" {{ old('status', 'active') === 'active' ? 'selected' : '' }}>Aktif</option>
                        <option value="on_hold" {{ old('status') === 'on_hold' ? 'selected' : '' }}>Ditunda</option>
                    </select>
                </div>
            </div>

            {{-- Actions --}}
            <div class="flex items-center justify-end gap-3 mt-6 pt-6 border-t border-gray-200 dark:border-gray-800">
                <a href="{{ route('kanban.projects.index') }}" class="px-4 py-2 text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800 rounded-lg transition">
                    Batal
                </a>
                <button type="submit" class="px-6 py-2 bg-brand-500 hover:bg-brand-600 text-white rounded-lg font-medium transition">
                    Buat Proyek
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

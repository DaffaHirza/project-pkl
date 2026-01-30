@extends('layouts.app')

@section('title', 'Tambah Objek Penilaian')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center gap-3">
        <a href="{{ route('appraisal.assets.index') }}" 
           class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
        </a>
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Tambah Objek Penilaian</h1>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Tambah objek baru untuk dinilai</p>
        </div>
    </div>

    <!-- Form -->
    <form action="{{ route('appraisal.assets.store') }}" method="POST" class="space-y-6">
        @csrf

        <div class="rounded-xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900">
            <div class="border-b border-gray-200 dark:border-gray-700 px-6 py-4">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Informasi Objek</h2>
            </div>
            <div class="p-6 space-y-6">
                <!-- Project Selection -->
                <div>
                    <label for="project_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        Proyek <span class="text-red-500">*</span>
                    </label>
                    <select id="project_id" name="project_id" required
                            class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white @error('project_id') border-red-500 @enderror">
                        <option value="">Pilih Proyek</option>
                        @foreach($projects as $proj)
                            <option value="{{ $proj->id }}" {{ old('project_id', $project->id ?? '') == $proj->id ? 'selected' : '' }}>
                                {{ $proj->project_code }} - {{ $proj->name }} ({{ $proj->client->name ?? 'No Client' }})
                            </option>
                        @endforeach
                    </select>
                    @error('project_id')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                    <!-- Asset Name -->
                    <div class="md:col-span-2">
                        <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            Nama Objek <span class="text-red-500">*</span>
                        </label>
                        <input type="text" id="name" name="name" value="{{ old('name') }}" required
                               placeholder="Contoh: Tanah dan Bangunan Jl. Sudirman No. 123"
                               class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white @error('name') border-red-500 @enderror">
                        @error('name')
                            <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Asset Type -->
                    <div>
                        <label for="asset_type" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            Jenis Aset <span class="text-red-500">*</span>
                        </label>
                        <select id="asset_type" name="asset_type" required
                                class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white @error('asset_type') border-red-500 @enderror">
                            <option value="">Pilih Jenis Aset</option>
                            @foreach($assetTypes as $key => $label)
                                <option value="{{ $key }}" {{ old('asset_type') == $key ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                        @error('asset_type')
                            <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Due Date -->
                    <div>
                        <label for="target_completion_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            Target Selesai
                        </label>
                        <input type="date" id="target_completion_date" name="target_completion_date" value="{{ old('target_completion_date') }}"
                               class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white @error('target_completion_date') border-red-500 @enderror">
                        @error('target_completion_date')
                            <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Location -->
                    <div class="md:col-span-2">
                        <label for="location_address" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            Lokasi/Alamat <span class="text-red-500">*</span>
                        </label>
                        <textarea id="location_address" name="location_address" rows="2" required
                                  placeholder="Alamat lengkap objek penilaian"
                                  class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white @error('location_address') border-red-500 @enderror">{{ old('location_address') }}</textarea>
                        @error('location_address')
                            <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Description -->
                    <div class="md:col-span-2">
                        <label for="description" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            Deskripsi
                        </label>
                        <textarea id="description" name="description" rows="3"
                                  placeholder="Deskripsi singkat objek penilaian (opsional)"
                                  class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white @error('description') border-red-500 @enderror">{{ old('description') }}</textarea>
                        @error('description')
                            <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Notes -->
                    <div class="md:col-span-2">
                        <label for="notes" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            Catatan Internal
                        </label>
                        <textarea id="notes" name="notes" rows="2"
                                  placeholder="Catatan internal untuk tim (opsional)"
                                  class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white @error('notes') border-red-500 @enderror">{{ old('notes') }}</textarea>
                        @error('notes')
                            <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>
        </div>

        <!-- Form Actions -->
        <div class="flex items-center justify-end gap-3">
            <a href="{{ route('appraisal.assets.index') }}" 
               class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white">
                Batal
            </a>
            <button type="submit" 
                    class="px-6 py-2 bg-brand-500 hover:bg-brand-600 text-white text-sm font-medium rounded-lg transition-colors">
                Simpan Objek
            </button>
        </div>
    </form>
</div>
@endsection

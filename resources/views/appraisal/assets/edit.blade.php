@extends('layouts.app')

@section('title', 'Edit Objek - ' . $asset->name)

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center gap-3">
        <a href="{{ route('appraisal.assets.show', $asset) }}" 
           class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
        </a>
        <div>
            <p class="text-sm text-gray-500 dark:text-gray-400">{{ $asset->asset_code }}</p>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Edit Objek Penilaian</h1>
        </div>
    </div>

    <!-- Form -->
    <form action="{{ route('appraisal.assets.update', $asset) }}" method="POST" class="space-y-6">
        @csrf
        @method('PUT')

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
                        @foreach($projects as $project)
                            <option value="{{ $project->id }}" {{ old('project_id', $asset->project_id) == $project->id ? 'selected' : '' }}>
                                {{ $project->project_code }} - {{ $project->name }} ({{ $project->client->name ?? 'No Client' }})
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
                        <input type="text" id="name" name="name" value="{{ old('name', $asset->name) }}" required
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
                            @foreach($assetTypes as $key => $label)
                                <option value="{{ $key }}" {{ old('asset_type', $asset->asset_type) == $key ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                        @error('asset_type')
                            <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Stage -->
                    <div>
                        <label for="current_stage" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            Stage
                        </label>
                        <select id="current_stage" name="current_stage"
                                class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white @error('current_stage') border-red-500 @enderror">
                            @foreach($stages as $key => $label)
                                <option value="{{ $key }}" {{ old('current_stage', $asset->current_stage) == $key ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                        @error('current_stage')
                            <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Priority -->
                    <div>
                        <label for="priority_status" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            Prioritas
                        </label>
                        <select id="priority_status" name="priority_status"
                                class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white @error('priority_status') border-red-500 @enderror">
                            <option value="normal" {{ old('priority_status', $asset->priority_status) == 'normal' ? 'selected' : '' }}>Normal</option>
                            <option value="warning" {{ old('priority_status', $asset->priority_status) == 'warning' ? 'selected' : '' }}>Warning</option>
                            <option value="critical" {{ old('priority_status', $asset->priority_status) == 'critical' ? 'selected' : '' }}>Critical</option>
                        </select>
                        @error('priority_status')
                            <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Due Date -->
                    <div>
                        <label for="target_completion_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            Target Selesai
                        </label>
                        <input type="date" id="target_completion_date" name="target_completion_date" 
                               value="{{ old('target_completion_date', $asset->target_completion_date?->format('Y-m-d')) }}"
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
                                  class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white @error('location_address') border-red-500 @enderror">{{ old('location_address', $asset->location_address) }}</textarea>
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
                                  class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white @error('description') border-red-500 @enderror">{{ old('description', $asset->description) }}</textarea>
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
                                  class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white @error('notes') border-red-500 @enderror">{{ old('notes', $asset->notes) }}</textarea>
                        @error('notes')
                            <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>
        </div>

        <!-- Form Actions -->
        <div class="flex items-center justify-between">
            <button type="button" onclick="confirmDelete()" 
                    class="px-4 py-2 text-sm font-medium text-red-600 hover:text-red-700 dark:text-red-400 dark:hover:text-red-300">
                Hapus Objek
            </button>
            <div class="flex items-center gap-3">
                <a href="{{ route('appraisal.assets.show', $asset) }}" 
                   class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white">
                    Batal
                </a>
                <button type="submit" 
                        class="px-6 py-2 bg-brand-500 hover:bg-brand-600 text-white text-sm font-medium rounded-lg transition-colors">
                    Simpan Perubahan
                </button>
            </div>
        </div>
    </form>
</div>

<!-- Delete Confirmation Modal -->
<div id="deleteModal" class="fixed inset-0 z-50 hidden overflow-y-auto">
    <div class="flex min-h-screen items-center justify-center p-4">
        <div class="fixed inset-0 bg-black/50" onclick="closeDeleteModal()"></div>
        <div class="relative w-full max-w-md rounded-xl bg-white dark:bg-gray-800 shadow-xl">
            <div class="p-6">
                <div class="flex items-center justify-center w-12 h-12 mx-auto mb-4 rounded-full bg-red-100 dark:bg-red-900/30">
                    <svg class="w-6 h-6 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                </div>
                <h3 class="text-lg font-semibold text-center text-gray-900 dark:text-white mb-2">Hapus Objek?</h3>
                <p class="text-sm text-center text-gray-500 dark:text-gray-400 mb-6">
                    Anda yakin ingin menghapus objek "{{ $asset->name }}"? Tindakan ini tidak dapat dibatalkan.
                </p>
                <div class="flex justify-center gap-3">
                    <button type="button" onclick="closeDeleteModal()" 
                            class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white">
                        Batal
                    </button>
                    <form action="{{ route('appraisal.assets.destroy', $asset) }}" method="POST" class="inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" 
                                class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white text-sm font-medium rounded-lg">
                            Ya, Hapus
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function confirmDelete() {
        document.getElementById('deleteModal').classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }

    function closeDeleteModal() {
        document.getElementById('deleteModal').classList.add('hidden');
        document.body.style.overflow = '';
    }

    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeDeleteModal();
        }
    });
</script>
@endpush
@endsection

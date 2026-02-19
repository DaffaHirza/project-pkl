@extends('layouts.app')

@section('title', 'Edit Asset - ' . $asset->name)

@section('content')
<div class="max-w-3xl mx-auto">
    {{-- Back Link --}}
    <a href="{{ route('kanban.assets.show', $asset) }}" class="inline-flex items-center gap-2 text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white mb-6">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
        </svg>
        Kembali ke Detail
    </a>

    {{-- Header --}}
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Edit Objek Penilaian</h1>
        <p class="text-gray-600 dark:text-gray-400 mt-1">{{ $asset->asset_code }} - {{ $asset->name }}</p>
    </div>

    @if($errors->any())
    <div class="mb-6 p-4 rounded-lg bg-red-50 border border-red-200 dark:bg-red-900/20 dark:border-red-800">
        <ul class="list-disc list-inside text-red-700 dark:text-red-400 text-sm">
            @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <form action="{{ route('kanban.assets.update', $asset) }}" method="POST" class="bg-white dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-800 p-6">
        @csrf
        @method('PUT')

        {{-- Project --}}
        <div class="mb-6">
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                Proyek <span class="text-red-500">*</span>
            </label>
            <select name="project_id" required class="w-full px-4 py-2 rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-brand-500">
                <option value="">Pilih Proyek</option>
                @foreach($projects as $project)
                <option value="{{ $project->id }}" {{ old('project_id', $asset->project_id) == $project->id ? 'selected' : '' }}>
                    {{ $project->name }} - {{ $project->client->name ?? 'No Client' }}
                </option>
                @endforeach
            </select>
        </div>

        {{-- Asset Code & Name --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Kode Asset <span class="text-red-500">*</span>
                </label>
                <input type="text" name="asset_code" value="{{ old('asset_code', $asset->asset_code) }}" required 
                       class="w-full px-4 py-2 rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-brand-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Nama Asset <span class="text-red-500">*</span>
                </label>
                <input type="text" name="name" value="{{ old('name', $asset->name) }}" required 
                       class="w-full px-4 py-2 rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-brand-500">
            </div>
        </div>

        {{-- Asset Type --}}
        <div class="mb-6">
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                Tipe Asset <span class="text-red-500">*</span>
            </label>
            <select name="asset_type" required class="w-full px-4 py-2 rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-brand-500">
                <option value="">Pilih Tipe</option>
                <option value="tanah" {{ old('asset_type', $asset->asset_type) === 'tanah' ? 'selected' : '' }}>Tanah</option>
                <option value="bangunan" {{ old('asset_type', $asset->asset_type) === 'bangunan' ? 'selected' : '' }}>Bangunan</option>
                <option value="tanah_bangunan" {{ old('asset_type', $asset->asset_type) === 'tanah_bangunan' ? 'selected' : '' }}>Tanah & Bangunan</option>
                <option value="kendaraan" {{ old('asset_type', $asset->asset_type) === 'kendaraan' ? 'selected' : '' }}>Kendaraan</option>
                <option value="mesin" {{ old('asset_type', $asset->asset_type) === 'mesin' ? 'selected' : '' }}>Mesin & Peralatan</option>
                <option value="bisnis" {{ old('asset_type', $asset->asset_type) === 'bisnis' ? 'selected' : '' }}>Bisnis</option>
                <option value="personal_property" {{ old('asset_type', $asset->asset_type) === 'personal_property' ? 'selected' : '' }}>Personal Property</option>
                <option value="lainnya" {{ old('asset_type', $asset->asset_type) === 'lainnya' ? 'selected' : '' }}>Lainnya</option>
            </select>
        </div>

        {{-- Location --}}
        <div class="mb-6">
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                Lokasi
            </label>
            <input type="text" name="location" value="{{ old('location', $asset->location) }}" 
                   placeholder="Alamat lengkap lokasi asset"
                   class="w-full px-4 py-2 rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-brand-500">
        </div>

        {{-- Description --}}
        <div class="mb-6">
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                Deskripsi
            </label>
            <textarea name="description" rows="3" 
                      class="w-full px-4 py-2 rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-brand-500">{{ old('description', $asset->description) }}</textarea>
        </div>

        {{-- Priority & Stage --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Prioritas
                </label>
                <select name="priority" class="w-full px-4 py-2 rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-brand-500">
                    <option value="normal" {{ old('priority', $asset->priority) === 'normal' ? 'selected' : '' }}>Normal</option>
                    <option value="warning" {{ old('priority', $asset->priority) === 'warning' ? 'selected' : '' }}>Warning</option>
                    <option value="critical" {{ old('priority', $asset->priority) === 'critical' ? 'selected' : '' }}>Kritikal</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Stage Saat Ini
                </label>
                <select name="current_stage" class="w-full px-4 py-2 rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-brand-500">
                    @foreach(\App\Models\ProjectAssetKanban::STAGES as $num => $name)
                    <option value="{{ $num }}" {{ old('current_stage', $asset->current_stage) == $num ? 'selected' : '' }}>{{ $num }}. {{ $name }}</option>
                    @endforeach
                </select>
                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Untuk pindah stage, disarankan gunakan tombol stage di halaman detail</p>
            </div>
        </div>

        {{-- Notes --}}
        <div class="mb-6">
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                Catatan Internal
            </label>
            <textarea name="notes" rows="2" 
                      class="w-full px-4 py-2 rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-brand-500">{{ old('notes', $asset->notes) }}</textarea>
        </div>

        {{-- Submit --}}
        <div class="flex items-center gap-4 pt-4 border-t border-gray-200 dark:border-gray-800">
            <button type="submit" class="px-6 py-2.5 bg-brand-500 hover:bg-brand-600 text-white rounded-lg font-medium transition">
                Simpan Perubahan
            </button>
            <a href="{{ route('kanban.assets.show', $asset) }}" class="px-6 py-2.5 text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white transition">
                Batal
            </a>
        </div>
    </form>

    {{-- Danger Zone --}}
    @if(auth()->user()->hasAdminAccess())
    <div class="mt-8 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-xl p-6">
        <h3 class="text-lg font-medium text-red-700 dark:text-red-400 mb-2">Zona Berbahaya</h3>
        <p class="text-red-600 dark:text-red-500 text-sm mb-4">Tindakan ini tidak dapat dibatalkan. Semua dokumen dan catatan terkait akan ikut terhapus.</p>
        <form action="{{ route('kanban.assets.destroy', $asset) }}" method="POST" onsubmit="return confirm('Anda yakin ingin menghapus asset ini beserta seluruh dokumen dan catatan?')">
            @csrf
            @method('DELETE')
            <button type="submit" class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg text-sm font-medium transition">
                Hapus Asset
            </button>
        </form>
    </div>
    @endif
</div>
@endsection

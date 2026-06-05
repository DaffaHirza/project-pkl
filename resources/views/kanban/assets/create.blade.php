@extends('layouts.app')

@section('title', 'Tambah Objek Penilaian')

@section('content')
    <div class="max-w-3xl mx-auto">
        {{-- Back Link --}}
        <a href="{{ route('kanban.assets.index') }}"
            class="inline-flex items-center gap-2 text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white mb-6">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            Kembali
        </a>

        {{-- Header --}}
        <div class="mb-6">
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Tambah Objek Penilaian</h1>
            <p class="text-gray-600 dark:text-gray-400 mt-1">Daftarkan asset baru untuk dinilai</p>
        </div>

        @if ($errors->any())
            <div class="mb-6 p-4 rounded-lg bg-red-50 border border-red-200 dark:bg-red-900/20 dark:border-red-800">
                <ul class="list-disc list-inside text-red-700 dark:text-red-400 text-sm">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('kanban.assets.store') }}" method="POST"
            class="bg-white dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-800 p-6">
            @csrf

            {{-- Client --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Kategori Klien <span class="text-red-500">*</span>
                    </label>
                    <select id="clientCategorySelect"
                        class="w-full px-4 py-2 rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-brand-500">
                        <option value="">Pilih Kategori</option>
                        <option value="bank">Bank</option>
                        <option value="pt_cv_induk">PT/CV Induk</option>
                        <option value="debitur">Debitur</option>
                        <option value="pt_cv_anak">PT/CV Anak</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Klien <span class="text-red-500">*</span>
                    </label>
                    <select id="clientSelect" name="client_id" required
                        class="w-full px-4 py-2 rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-brand-500">
                        <option value="">Pilih kategori terlebih dahulu</option>

                        @foreach ($clientGroups['bank'] as $client)
                            <option value="{{ $client->id }}" data-category="bank"
                                {{ old('client_id', $selectedClientId ?? ($asset->client_id ?? '')) == $client->id ? 'selected' : '' }}>
                                {{ $client->company_name ?? $client->name }}
                            </option>
                        @endforeach

                        @foreach ($clientGroups['pt_cv_induk'] as $client)
                            <option value="{{ $client->id }}" data-category="pt_cv_induk"
                                {{ old('client_id', $selectedClientId ?? ($asset->client_id ?? '')) == $client->id ? 'selected' : '' }}>
                                {{ $client->company_name ?? $client->name }}
                            </option>
                        @endforeach

                        @foreach ($clientGroups['debitur'] as $client)
                            <option value="{{ $client->id }}" data-category="debitur"
                                {{ old('client_id', $selectedClientId ?? ($asset->client_id ?? '')) == $client->id ? 'selected' : '' }}>
                                {{ $client->name }}
                            </option>
                        @endforeach

                        @foreach ($clientGroups['pt_cv_anak'] as $client)
                            <option value="{{ $client->id }}" data-category="pt_cv_anak"
                                {{ old('client_id', $selectedClientId ?? ($asset->client_id ?? '')) == $client->id ? 'selected' : '' }}>
                                {{ $client->company_name ?? $client->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            {{-- Asset Name --}}
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Nama Asset <span class="text-red-500">*</span>
                </label>
                <input type="text" name="name" value="{{ old('name') }}" required
                    placeholder="Gedung Kantor, Tanah, dll"
                    class="w-full px-4 py-2 rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-brand-500">
            </div>

            {{-- Asset Type --}}
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Tipe Asset <span class="text-red-500">*</span>
                </label>
                <select name="asset_type" required
                    class="w-full px-4 py-2 rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-brand-500">
                    <option value="">Pilih Tipe</option>
                    <option value="tanah" {{ old('asset_type') === 'tanah' ? 'selected' : '' }}>Tanah</option>
                    <option value="bangunan" {{ old('asset_type') === 'bangunan' ? 'selected' : '' }}>Bangunan</option>
                    <option value="tanah_bangunan" {{ old('asset_type') === 'tanah_bangunan' ? 'selected' : '' }}>Tanah &
                        Bangunan</option>
                    <option value="kendaraan" {{ old('asset_type') === 'kendaraan' ? 'selected' : '' }}>Kendaraan</option>
                    <option value="mesin" {{ old('asset_type') === 'mesin' ? 'selected' : '' }}>Mesin & Peralatan</option>
                    <option value="bisnis" {{ old('asset_type') === 'bisnis' ? 'selected' : '' }}>Bisnis</option>
                    <option value="personal_property" {{ old('asset_type') === 'personal_property' ? 'selected' : '' }}>
                        Personal Property</option>
                    <option value="lainnya" {{ old('asset_type') === 'lainnya' ? 'selected' : '' }}>Lainnya</option>
                </select>
            </div>

            {{-- Location --}}
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Lokasi
                </label>
                <input type="text" name="location" value="{{ old('location') }}"
                    placeholder="Alamat lengkap lokasi asset"
                    class="w-full px-4 py-2 rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-brand-500">
            </div>

            {{-- Initial Stage --}}
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Stage Awal
                </label>
                <select name="current_stage"
                    class="w-full px-4 py-2 rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-brand-500">
                    @foreach (\App\Models\AssetKanban::STAGES as $num => $name)
                        <option value="{{ $num }}" {{ old('current_stage', 1) == $num ? 'selected' : '' }}>
                            {{ $num }}. {{ $name }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Notes --}}
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Catatan Internal
                </label>
                <textarea name="notes" rows="2" placeholder="Catatan untuk tim internal"
                    class="w-full px-4 py-2 rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-brand-500">{{ old('notes') }}</textarea>
            </div>

            {{-- Submit --}}
            <div class="flex items-center gap-4 pt-4 border-t border-gray-200 dark:border-gray-800">
                <button type="submit"
                    class="px-6 py-2.5 bg-brand-500 hover:bg-brand-600 text-white rounded-lg font-medium transition">
                    Simpan Asset
                </button>
                <a href="{{ route('kanban.assets.index') }}"
                    class="px-6 py-2.5 text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white transition">
                    Batal
                </a>
            </div>
        </form>
    </div>
    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const categorySelect = document.getElementById('clientCategorySelect');
                const clientSelect = document.getElementById('clientSelect');
                const allOptions = Array.from(clientSelect.querySelectorAll('option[data-category]'));

                function detectSelectedCategory() {
                    const selected = clientSelect.querySelector('option:checked');

                    if (selected && selected.dataset.category) {
                        categorySelect.value = selected.dataset.category;
                    }
                }

                function filterClients() {
                    const category = categorySelect.value;
                    const selectedValue = clientSelect.value;

                    clientSelect.innerHTML = '';

                    const placeholder = document.createElement('option');
                    placeholder.value = '';
                    placeholder.textContent = category ? 'Pilih klien' : 'Pilih kategori terlebih dahulu';
                    clientSelect.appendChild(placeholder);

                    allOptions.forEach(option => {
                        if (!category || option.dataset.category === category) {
                            clientSelect.appendChild(option.cloneNode(true));
                        }
                    });

                    if (selectedValue) {
                        clientSelect.value = selectedValue;
                    }
                }

                detectSelectedCategory();
                filterClients();

                categorySelect.addEventListener('change', function() {
                    clientSelect.value = '';
                    filterClients();
                });
            });
        </script>
    @endpush
@endsection

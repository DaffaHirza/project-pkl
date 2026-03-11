@extends('layouts.app')

@section('title', 'Tambah Klien')

@section('content')
<div>
    {{-- Header --}}
    <div class="mb-6">
        <a href="{{ route('kanban.clients.create') }}" class="inline-flex items-center text-sm text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300 mb-2">
            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
            </svg>
            Kembali
        </a>
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-lg bg-orange-100 dark:bg-orange-900/30 flex items-center justify-center">
                <svg class="w-5 h-5 text-orange-600 dark:text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                </svg>
            </div>
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Tambah Klien</h1>
                <p class="text-gray-600 dark:text-gray-400">Tambah debitur atau anak perusahaan</p>
            </div>
        </div>
    </div>

    {{-- Error Messages --}}
    @if($errors->any())
    <div class="mb-6 p-4 rounded-lg bg-red-50 border border-red-200 dark:bg-red-900/20 dark:border-red-800">
        <ul class="list-disc list-inside text-red-700 dark:text-red-400 text-sm">
            @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <form action="{{ route('kanban.clients.store.klien') }}" method="POST">
        @csrf
        
        <div class="bg-white dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-800 p-6 mb-6">
            <div class="flex items-center gap-2 mb-4">
                <svg class="w-5 h-5 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Informasi Klien</h2>
            </div>
            
            <div class="space-y-6">
                {{-- Client Type --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        Tipe Klien <span class="text-red-500">*</span>
                    </label>
                    <select name="client_type" id="clientType" required
                            class="w-full md:w-1/2 px-4 py-2 rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-orange-500 focus:border-transparent">
                        <option value="">Pilih Tipe</option>
                        <option value="debitur" {{ old('client_type') === 'debitur' ? 'selected' : '' }}>Debitur (milik Bank)</option>
                        <option value="pt_cv_anak" {{ old('client_type') === 'pt_cv_anak' ? 'selected' : '' }}>PT/CV Anak (milik PT/CV Induk)</option>
                    </select>
                </div>

                {{-- Parent Company --}}
                <div id="parentGroup">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        <span id="parentLabel">Induk</span> <span class="text-red-500">*</span>
                    </label>
                    <select name="parent_id" id="parentSelect" required
                            class="w-full md:w-1/2 px-4 py-2 rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-orange-500 focus:border-transparent">
                        <option value="">Pilih Induk</option>
                        @foreach($parentCompanies as $parent)
                        <option value="{{ $parent->id }}" data-type="{{ $parent->type }}" {{ old('parent_id') == $parent->id ? 'selected' : '' }}>
                            {{ $parent->company_name ?? $parent->name }} ({{ $parent->type === 'bank' ? 'Bank' : 'PT/CV' }})
                        </option>
                        @endforeach
                    </select>
                    @if($parentCompanies->isEmpty())
                    <p class="text-sm text-yellow-600 dark:text-yellow-400 mt-2">
                        Belum ada perusahaan terdaftar. <a href="{{ route('kanban.clients.create.bank') }}" class="underline">Tambah bank</a> atau <a href="{{ route('kanban.clients.create.perusahaan-induk') }}" class="underline">PT/CV</a> terlebih dahulu.
                    </p>
                    @endif
                </div>

                {{-- Client Name --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        Nama <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="name" value="{{ old('name') }}" required
                           placeholder="Nama debitur atau anak perusahaan"
                           class="w-full md:w-1/2 px-4 py-2 rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-orange-500 focus:border-transparent">
                </div>

                {{-- Company Name (optional for debitur) --}}
                <div id="companyNameGroup">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        Nama Perusahaan <span id="companyNameRequired" class="text-red-500 hidden">*</span>
                        <span id="companyNameOptional" class="text-gray-400">(Opsional)</span>
                    </label>
                    <input type="text" name="company_name" id="companyNameInput" value="{{ old('company_name') }}"
                           placeholder="Nama PT/CV jika ada"
                           class="w-full md:w-1/2 px-4 py-2 rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-orange-500 focus:border-transparent">
                </div>
            </div>
        </div>

        {{-- Submit --}}
        <div class="flex items-center justify-end gap-3">
            <a href="{{ route('kanban.clients.index') }}" class="px-6 py-2.5 border border-gray-300 dark:border-gray-700 text-gray-700 dark:text-gray-300 rounded-lg font-medium hover:bg-gray-50 dark:hover:bg-gray-800 transition">
                Batal
            </a>
            <button type="submit" class="px-6 py-2.5 bg-orange-500 hover:bg-orange-600 text-white rounded-lg font-medium transition">
                Simpan Klien
            </button>
        </div>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const clientType = document.getElementById('clientType');
    const parentSelect = document.getElementById('parentSelect');
    const parentLabel = document.getElementById('parentLabel');
    const companyNameInput = document.getElementById('companyNameInput');
    const companyNameRequired = document.getElementById('companyNameRequired');
    const companyNameOptional = document.getElementById('companyNameOptional');

    function updateFormFields() {
        const type = clientType.value;
        
        // Filter parent options based on type
        Array.from(parentSelect.options).forEach(opt => {
            if (!opt.value) {
                opt.style.display = ''; // Always show empty option
                return;
            }
            
            const parentType = opt.dataset.type;
            if (type === 'debitur') {
                // Debitur should only see bank parents
                opt.style.display = parentType === 'bank' ? '' : 'none';
            } else if (type === 'pt_cv_anak') {
                // PT/CV Anak should only see pt_cv parents
                opt.style.display = parentType === 'pt_cv' ? '' : 'none';
            } else {
                opt.style.display = '';
            }
        });

        // Update parent label
        if (type === 'debitur') {
            parentLabel.textContent = 'Bank Induk';
            companyNameInput.removeAttribute('required');
            companyNameRequired.classList.add('hidden');
            companyNameOptional.classList.remove('hidden');
        } else if (type === 'pt_cv_anak') {
            parentLabel.textContent = 'PT/CV Induk';
            companyNameInput.setAttribute('required', 'required');
            companyNameRequired.classList.remove('hidden');
            companyNameOptional.classList.add('hidden');
        } else {
            parentLabel.textContent = 'Induk';
        }

        // Reset parent selection when type changes
        parentSelect.value = '';
    }

    clientType.addEventListener('change', updateFormFields);
    
    // Initialize on page load
    updateFormFields();
});
</script>
@endsection

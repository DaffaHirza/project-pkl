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
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-lg {{ $client->type === 'bank' ? 'bg-blue-100 dark:bg-blue-900/30' : ($client->type === 'pt_cv' ? 'bg-purple-100 dark:bg-purple-900/30' : 'bg-orange-100 dark:bg-orange-900/30') }} flex items-center justify-center">
                <svg class="w-5 h-5 {{ $client->type === 'bank' ? 'text-blue-600 dark:text-blue-400' : ($client->type === 'pt_cv' ? 'text-purple-600 dark:text-purple-400' : 'text-orange-600 dark:text-orange-400') }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                </svg>
            </div>
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Edit Klien</h1>
                <p class="text-gray-600 dark:text-gray-400">{{ $client->name }}</p>
            </div>
        </div>
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

                {{-- SPK Number (for Bank and PT/CV Induk) --}}
                <div id="spkField" class="{{ in_array($client->type, ['bank', 'pt_cv']) && !$client->parent_id ? '' : 'hidden' }}">
                    <label for="spk_number" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        Nomor SPK
                    </label>
                    <input type="text" name="spk_number" id="spk_number" value="{{ old('spk_number', $client->spk_number) }}"
                           placeholder="Contoh: SPK/001/2026"
                           class="w-full px-4 py-2 rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-brand-500 focus:border-transparent @error('spk_number') border-red-500 @enderror">
                    @error('spk_number')
                    <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Type --}}
                <div>
                    <label for="type" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        Tipe Klien <span class="text-red-500">*</span>
                    </label>
                    <select name="type" id="type" required
                            class="w-full px-4 py-2 rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-brand-500 focus:border-transparent @error('type') border-red-500 @enderror">
                        <option value="">Pilih Tipe</option>
                        <option value="bank" {{ old('type', $client->type) === 'bank' ? 'selected' : '' }}>Bank</option>
                        <option value="pt_cv" {{ old('type', $client->type) === 'pt_cv' ? 'selected' : '' }}>PT/CV</option>
                        <option value="debitur" {{ old('type', $client->type) === 'debitur' ? 'selected' : '' }}>Debitur</option>
                    </select>
                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                        Bank = Pemberi order, PT/CV = Perusahaan induk/anak, Debitur = Pemilik asset
                    </p>
                    @error('type')
                    <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Parent Client --}}
                <div id="parentField" class="hidden">
                    <label for="parent_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        <span id="parentLabel">Induk</span>
                    </label>
                    <select name="parent_id" id="parent_id"
                            class="w-full px-4 py-2 rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-brand-500 focus:border-transparent">
                        <option value="">Tidak Ada</option>
                        @foreach($parentClients as $parent)
                        <option value="{{ $parent->id }}" 
                                data-type="{{ $parent->type }}"
                                {{ old('parent_id', $client->parent_id) == $parent->id ? 'selected' : '' }}>
                            {{ $parent->name }} ({{ $parent->type === 'bank' ? 'Bank' : 'PT/CV' }})
                        </option>
                        @endforeach
                    </select>
                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400" id="parentHelp">
                        Pilih Bank untuk debitur atau PT induk untuk PT anak
                    </p>
                    @error('parent_id')
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

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const typeSelect = document.getElementById('type');
    const parentField = document.getElementById('parentField');
    const parentSelect = document.getElementById('parent_id');
    const parentLabel = document.getElementById('parentLabel');
    const parentHelp = document.getElementById('parentHelp');
    const spkField = document.getElementById('spkField');
    
    function updateParentField() {
        const type = typeSelect.value;
        const hasParent = parentSelect.value !== '';
        
        // Show/hide SPK field (for bank and PT/CV induk - no parent)
        if (type === 'bank' || (type === 'pt_cv' && !hasParent)) {
            spkField.classList.remove('hidden');
        } else {
            spkField.classList.add('hidden');
        }
        
        if (type === 'debitur') {
            parentField.classList.remove('hidden');
            parentLabel.textContent = 'Bank (Pemberi Order)';
            parentHelp.textContent = 'Pilih Bank yang memberikan order untuk debitur ini';
            // Filter to show only bank
            Array.from(parentSelect.options).forEach(opt => {
                if (opt.value && opt.dataset.type !== 'bank') {
                    opt.style.display = 'none';
                } else {
                    opt.style.display = '';
                }
            });
        } else if (type === 'pt_cv') {
            parentField.classList.remove('hidden');
            parentLabel.textContent = 'PT/CV Induk (Opsional)';
            parentHelp.textContent = 'Pilih perusahaan induk jika ini adalah PT anak';
            // Filter to show only pt_cv
            Array.from(parentSelect.options).forEach(opt => {
                if (opt.value && opt.dataset.type !== 'pt_cv') {
                    opt.style.display = 'none';
                } else {
                    opt.style.display = '';
                }
            });
        } else {
            parentField.classList.add('hidden');
            parentSelect.value = '';
        }
    }
    
    typeSelect.addEventListener('change', updateParentField);
    parentSelect.addEventListener('change', updateParentField);
    
    // Initialize on load
    updateParentField();
});
</script>
@endpush
@endsection

@extends('layouts.app')

@section('title', 'Tambah Bank')

@section('content')
    <div>
        {{-- Header --}}
        <div class="mb-6">
            <a href="{{ route('kanban.clients.create') }}"
                class="inline-flex items-center text-sm text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300 mb-2">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
                Kembali
            </a>
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-lg bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center">
                    <svg class="w-5 h-5 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                    </svg>
                </div>
                <div>
                    <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Tambah Bank</h1>
                    <p class="text-gray-600 dark:text-gray-400">Isi data bank dan debitur</p>
                </div>
            </div>
        </div>

        {{-- Error Messages --}}
        @if ($errors->any())
            <div class="mb-6 p-4 rounded-lg bg-red-50 border border-red-200 dark:bg-red-900/20 dark:border-red-800">
                <ul class="list-disc list-inside text-red-700 dark:text-red-400 text-sm">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('kanban.clients.store.bank') }}" method="POST" id="bankForm">
            @csrf

            {{-- Bank Info --}}
            <div class="bg-white dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-800 p-6 mb-6">
                <div class="flex items-center gap-2 mb-4">
                    <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Informasi Bank</h2>
                </div>

                <div class="grid md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            Nama Bank <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="company_name" value="{{ old('company_name') }}" required
                            placeholder="Contoh: Bank BCA"
                            class="w-full px-4 py-2 rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            Nomor SPK
                        </label>
                        <input type="text" name="spk_number" value="{{ old('spk_number') }}"
                            placeholder="Contoh: SPK/001/2026"
                            class="w-full px-4 py-2 rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>
                </div>
            </div>

            {{-- Debitur List --}}
            <div class="bg-white dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-800 p-6 mb-6">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Daftar Debitur</h2>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Minimal 1 debitur harus ditambahkan</p>
                    </div>
                    <button type="button" id="addDebiturBtn"
                        class="inline-flex items-center gap-1 px-3 py-1.5 bg-green-100 hover:bg-green-200 dark:bg-green-900/30 dark:hover:bg-green-900/50 text-green-700 dark:text-green-400 rounded-lg text-sm font-medium transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        Tambah Debitur
                    </button>
                </div>

                <div id="debiturContainer" class="space-y-4">
                    {{-- Debitur rows will be added here --}}
                </div>

                <p id="noDebiturMessage" class="hidden text-center text-gray-500 dark:text-gray-400 py-8">
                    Klik "Tambah Debitur" untuk menambahkan debitur.
                </p>
            </div>

            {{-- Submit --}}
            <div class="flex items-center justify-end gap-3">
                <a href="{{ route('kanban.clients.index') }}"
                    class="px-6 py-2.5 border border-gray-300 dark:border-gray-700 text-gray-700 dark:text-gray-300 rounded-lg font-medium hover:bg-gray-50 dark:hover:bg-gray-800 transition">
                    Batal
                </a>
                <button type="submit"
                    class="px-6 py-2.5 bg-blue-500 hover:bg-blue-600 text-white rounded-lg font-medium transition">
                    Simpan Bank
                </button>
            </div>
        </form>
    </div>

    {{-- Debitur Row Template --}}
    <template id="debiturRowTemplate">
        <div
            class="debitur-row flex items-start gap-4 p-4 bg-gray-50 dark:bg-gray-800/50 rounded-lg border border-gray-200 dark:border-gray-700">
            <div class="flex-1 grid md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        Nama Debitur <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="debiturs[INDEX][name]" required placeholder="Nama lengkap debitur"
                        class="w-full px-4 py-2 rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Nama Perusahaan
                        (Opsional)</label>
                    <input type="text" name="debiturs[INDEX][company_name]" placeholder="PT/CV milik debitur"
                        class="w-full px-4 py-2 rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                </div>
            </div>
            <button type="button"
                class="remove-debitur mt-6 p-2 text-red-500 hover:text-red-700 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-lg transition"
                title="Hapus">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                </svg>
            </button>
        </div>
    </template>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const container = document.getElementById('debiturContainer');
                const template = document.getElementById('debiturRowTemplate');
                const addBtn = document.getElementById('addDebiturBtn');
                const noMessage = document.getElementById('noDebiturMessage');
                let debiturIndex = 0;

                function updateNoMessage() {
                    const rows = container.querySelectorAll('.debitur-row');
                    noMessage.classList.toggle('hidden', rows.length > 0);
                }

                function addDebitur() {
                    const clone = template.content.cloneNode(true);
                    const html = clone.querySelector('.debitur-row').outerHTML.replace(/INDEX/g, debiturIndex);
                    container.insertAdjacentHTML('beforeend', html);
                    debiturIndex++;
                    updateNoMessage();
                }

                // Add first debitur by default
                addDebitur();

                addBtn.addEventListener('click', addDebitur);

                container.addEventListener('click', function(e) {
                    if (e.target.closest('.remove-debitur')) {
                        const row = e.target.closest('.debitur-row');
                        const rows = container.querySelectorAll('.debitur-row');
                        if (rows.length > 1) {
                            row.remove();
                            updateNoMessage();
                        } else {
                            alert('Minimal 1 debitur harus ada.');
                        }
                    }
                });
            });
        </script>
    @endpush
@endsection

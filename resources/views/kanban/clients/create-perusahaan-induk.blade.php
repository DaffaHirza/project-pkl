@extends('layouts.app')

@section('title', 'Tambah PT/CV Induk')

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
                <div class="w-10 h-10 rounded-lg bg-purple-100 dark:bg-purple-900/30 flex items-center justify-center">
                    <svg class="w-5 h-5 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 21v-4m0 0V5a2 2 0 012-2h6.5l1 1H21l-3 6 3 6h-8.5l-1-1H5a2 2 0 00-2 2zm9-13.5V9" />
                    </svg>
                </div>
                <div>
                    <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Tambah PT/CV Induk</h1>
                    <p class="text-gray-600 dark:text-gray-400">Isi data perusahaan induk dan anak perusahaan</p>
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

        <form action="{{ route('kanban.clients.store.perusahaan-induk') }}" method="POST" id="perusahaanForm">
            @csrf

            {{-- Company Info --}}
            <div class="bg-white dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-800 p-6 mb-6">
                <div class="flex items-center gap-2 mb-4">
                    <svg class="w-5 h-5 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Informasi Perusahaan Induk</h2>
                </div>

                <div class="grid md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            Nama Perusahaan <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="company_name" value="{{ old('company_name') }}" required
                            placeholder="Contoh: PT ABC Indonesia"
                            class="w-full px-4 py-2 rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            Nomor SPK
                        </label>
                        <input type="text" name="spk_number" value="{{ old('spk_number') }}"
                            placeholder="Contoh: SPK/001/2026"
                            class="w-full px-4 py-2 rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                    </div>
                </div>
            </div>

            {{-- Children List --}}
            <div class="bg-white dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-800 p-6 mb-6">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Anak Perusahaan</h2>
                    <button type="button" id="addChildBtn"
                        class="inline-flex items-center gap-1 px-3 py-1.5 bg-green-100 hover:bg-green-200 dark:bg-green-900/30 dark:hover:bg-green-900/50 text-green-700 dark:text-green-400 rounded-lg text-sm font-medium transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        Tambah Anak
                    </button>
                </div>

                <div id="childrenContainer" class="space-y-4">
                    {{-- Child rows will be added here --}}
                </div>

                <p id="noChildMessage" class="text-center text-gray-500 dark:text-gray-400 py-8">
                    Anak perusahaan opsional. Klik "Tambah Anak" untuk menambahkan.
                </p>
            </div>

            {{-- Submit --}}
            <div class="flex items-center justify-end gap-3">
                <a href="{{ route('kanban.clients.index') }}"
                    class="px-6 py-2.5 border border-gray-300 dark:border-gray-700 text-gray-700 dark:text-gray-300 rounded-lg font-medium hover:bg-gray-50 dark:hover:bg-gray-800 transition">
                    Batal
                </a>
                <button type="submit"
                    class="px-6 py-2.5 bg-purple-500 hover:bg-purple-600 text-white rounded-lg font-medium transition">
                    Simpan Perusahaan
                </button>
            </div>
        </form>
    </div>

    {{-- Child Row Template --}}
    <template id="childRowTemplate">
        <div
            class="child-row flex items-start gap-4 p-4 bg-gray-50 dark:bg-gray-800/50 rounded-lg border border-gray-200 dark:border-gray-700">
            <div class="flex-1">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Nama Anak Perusahaan</label>
                <input type="text" name="children[INDEX][company_name]" required
                    placeholder="Contoh: PT ABC Cabang Surabaya"
                    class="w-full px-4 py-2 rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-green-500 focus:border-transparent">
            </div>
            <button type="button" class="remove-child p-2 text-gray-400 hover:text-red-500 transition mt-6" title="Hapus">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                </svg>
            </button>
        </div>
    </template>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const container = document.getElementById('childrenContainer');
            const template = document.getElementById('childRowTemplate');
            const addBtn = document.getElementById('addChildBtn');
            const noMessage = document.getElementById('noChildMessage');
            let childIndex = 0;

            function updateNoMessage() {
                const rows = container.querySelectorAll('.child-row');
                noMessage.style.display = rows.length === 0 ? 'block' : 'none';
            }

            function addChildRow() {
                const clone = template.content.cloneNode(true);
                const row = clone.querySelector('.child-row');

                // Update index in input names
                row.innerHTML = row.innerHTML.replace(/INDEX/g, childIndex);

                // Add remove listener
                row.querySelector('.remove-child').addEventListener('click', function() {
                    row.remove();
                    updateNoMessage();
                });

                container.appendChild(row);
                childIndex++;
                updateNoMessage();

                // Focus first input
                row.querySelector('input').focus();
            }

            addBtn.addEventListener('click', addChildRow);
            updateNoMessage();
        });
    </script>
@endsection

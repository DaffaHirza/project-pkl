@extends('layouts.app')

@section('title', 'AI Assistant')

@section('content')
    <!-- Page Header -->
    <div class="flex items-center justify-between pb-5">
        <div class="w-full flex items-center justify-between dark:border-gray-700">
            <div class="flex flex-col">
                <h1 class="text-gray-dark dark:text-white text-2xl font-semibold">Masukan Dokumen</h1>
                <span class="text-xs text-gray-400">Unggah dokumen untuk diproses oleh AI Assistant</span>
            </div>
            <a href="/assistantai"
                class="group flex border-2 border-gray-300 rounded-xl items-center justify-center px-3 py-2 gap-1 cursor-pointer hover:bg-gray-100 transition-colors">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M15 18L9 12L15 6" stroke="#9CA3AF" stroke-width="2" stroke-linecap="round"
                        stroke-linejoin="round" class="group-hover:stroke-blue-600 transition-colors" />
                </svg>
                <p class="text-gray-600 font-medium group-hover:text-blue-600 transition-colors">Kembali</p>
            </a>
        </div>
    </div>
    <!-- Card -->
    <div class="w-full mx-auto mb-10 rounded-xl shadow-sm overflow-hidden dark:bg-neutral-900">
        <form id="uploadForm" action="{{ route('assistantai.pages.store') }}" method="POST" enctype="multipart/form-data"
            class="p-6 bg-white rounded-xl shadow-sm">
            @csrf
            @include('assistantai.pages.components.upload')

            <div class="flex flex-col sm:flex-row justify-between items-center gap-4 mt-6 pt-6 border-gray-100">

                <button type="submit" name="action" value="analyze" id="btnAnalyze"
                    class="w-full sm:w-auto flex items-center justify-center font-medium text-white bg-brand-500 hover:bg-brand-600 rounded-xl px-6 py-2.5 gap-2 transition-all">
                    <span id="iconAnalyze">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none"
                            xmlns="http://www.w3.org/2000/svg">
                            <path fill-rule="evenodd" clip-rule="evenodd"
                                d="M7.08443 11.0844L8.55131 6.68377H10.4487L11.9156 11.0844L16.3162 12.5513V14.4487L11.9156 15.9156L10.4487 20.3162H8.55131L7.08443 15.9156L2.68377 14.4487V12.5513L7.08443 11.0844ZM9.5 10.1623L8.82368 12.1912L8.19123 12.8237L6.16227 13.5L8.19123 14.1763L8.82368 14.8088L9.5 16.8377L10.1763 14.8088L10.8088 14.1763L12.8377 13.5L10.8088 12.8237L10.1763 12.1912L9.5 10.1623Z"
                                fill="white" />
                            <path fill-rule="evenodd" clip-rule="evenodd"
                                d="M16.1507 5.15066L16.9308 2.81026H18.0692L18.8493 5.15066L21.1897 5.93079V7.06921L18.8493 7.84934L18.0692 10.1897H16.9308L16.1507 7.84934L13.8103 7.06921V5.93079L16.1507 5.15066Z"
                                fill="white" />
                        </svg>
                    </span>
                    <span id="loadingAnalyze" class="hidden animate-spin">
                        <svg class="w-5 h-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none"
                            viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor"
                                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                            </path>
                        </svg>
                    </span>
                    <span id="textAnalyze">Analisis Dokumen</span>
                </button>

                <button type="submit" name="action" value="savedraft"
                    class="w-full sm:w-auto flex items-center justify-center font-medium text-white bg-gray-dark hover:bg-gray-900 rounded-xl px-6 py-2.5 gap-2 transition-all">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none"
                        xmlns="http://www.w3.org/2000/svg">
                        <path
                            d="M7 3V6.4C7 6.96005 7 7.24008 7.10899 7.45399C7.20487 7.64215 7.35785 7.79513 7.54601 7.89101C7.75992 8 8.03995 8 8.6 8H15.4C15.9601 8 16.2401 8 16.454 7.89101C16.6422 7.79513 16.7951 7.64215 16.891 7.45399C17 7.24008 17 6.96005 17 6.4V4M17 21V14.6C17 14.0399 17 13.7599 16.891 13.546C16.7951 13.3578 16.6422 13.2049 16.454 13.109C16.2401 13 15.9601 13 15.4 13H8.6C8.03995 13 7.75992 13 7.54601 13.109C7.35785 13.2049 7.20487 13.3578 7.10899 13.546C7 13.7599 7 14.0399 7 14.6V21M21 9.32548V16.2C21 17.8802 21 18.7202 20.673 19.362C20.3854 19.9265 19.9265 20.3854 19.362 20.673C18.7202 21 17.8802 21 16.2 21H7.8C6.11984 21 5.27976 21 4.63803 20.673C4.07354 20.3854 3.6146 19.9265 3.32698 19.362C3 18.7202 3 17.8802 3 16.2V7.8C3 6.11984 3 5.27976 3.32698 4.63803C3.6146 4.07354 4.07354 3.6146 4.63803 3.32698C5.27976 3 6.11984 3 7.8 3H14.6745C15.1637 3 15.4083 3 15.6385 3.05526C15.8425 3.10425 16.0376 3.18506 16.2166 3.29472C16.4184 3.4184 16.5914 3.59135 16.9373 3.93726L20.0627 7.06274C20.4086 7.40865 20.5816 7.5816 20.7053 7.78343C20.8149 7.96237 20.8957 8.15746 20.9447 8.36154C21 8.59171 21 8.8363 21 9.32548Z"
                            stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                    Simpan Hasil (Draft)
                </button>
            </div>
        </form>
    </div>
    <!-- End Card -->
    <div class="border-b"></div>
    <!-- Hasil AI -->
    @include('assistantai.pages.components.hasil')
    <!-- End Hasil AI -->
    <script>
        // 1. Logic Upload File
        function handleFileSelect(input, idSuffix) {
            const file = input.files[0];

            const defaultViewId = idSuffix === 'main' ? 'main-default' : 'default-' + idSuffix;
            const previewViewId = idSuffix === 'main' ? 'main-preview' : 'preview-' + idSuffix;
            const filenameId = idSuffix === 'main' ? 'main-filename' : 'filename-' + idSuffix;

            const defaultView = document.getElementById(defaultViewId);
            const previewView = document.getElementById(previewViewId);
            const filenameLabel = document.getElementById(filenameId);

            if (file) {
                filenameLabel.textContent = file.name;

                // LEBIH AMAN: Sembunyikan default secara eksplisit
                if (defaultView) defaultView.classList.add('hidden');

                previewView.classList.remove('hidden');
                previewView.classList.add('flex');
            }
        }

        function removeFile(idSuffix) {
            const inputId = idSuffix === 'main' ? 'main-upload' : 'upload-' + idSuffix;
            const defaultViewId = idSuffix === 'main' ? 'main-default' : 'default-' + idSuffix;
            const previewViewId = idSuffix === 'main' ? 'main-preview' : 'preview-' + idSuffix;

            document.getElementById(inputId).value = '';

            // Kembalikan tampilan
            const defaultView = document.getElementById(defaultViewId);
            const previewView = document.getElementById(previewViewId);

            if (defaultView) defaultView.classList.remove('hidden'); // Munculkan lagi default

            previewView.classList.add('hidden');
            previewView.classList.remove('flex');
        }

        // 2. Logic Loading State (Agar User tidak Spam Klik)
        document.getElementById('uploadForm').addEventListener('submit', function(e) {
            // Cek tombol mana yang diklik (kita deteksi lewat active element atau event submitter)
            const submitter = e.submitter;

            if (submitter && submitter.value === 'analyze') {
                const btn = document.getElementById('btnAnalyze');
                const icon = document.getElementById('iconAnalyze');
                const loading = document.getElementById('loadingAnalyze');
                const text = document.getElementById('textAnalyze');

                // Matikan tombol
                btn.disabled = true;
                btn.classList.add('opacity-75', 'cursor-not-allowed');

                // Ganti icon jadi spinner
                icon.classList.add('hidden');
                loading.classList.remove('hidden');
                text.textContent = 'Sedang Menganalisis...';
            }
        });

        document.getElementById('uploadForm').addEventListener('submit', function(e) {
            // Cek tombol mana yang diklik
            const submitter = e.submitter;

            // Jika yang diklik adalah tombol ANALYZE
            if (submitter && submitter.value === 'analyze') {
                const btn = document.getElementById('btnAnalyze');
                const icon = document.getElementById('iconAnalyze');
                const loading = document.getElementById('loadingAnalyze');
                const text = document.getElementById('textAnalyze');

                // --- TRIK JITU DISINI ---
                // Karena tombol mau dimatikan, kita buat input palsu (hidden)
                // supaya server tetap tahu kalau kita mau "analyze"
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'action';
                input.value = 'analyze';
                this.appendChild(input);
                // -----------------------

                // Baru sekarang kita matikan tombolnya (Visual Only)
                // Kita pakai CSS pointer-events none biar lebih aman daripada disabled=true
                // tapi disabled=true juga oke asalkan ada hidden input di atas.
                btn.classList.add('opacity-75', 'cursor-not-allowed');

                // Ganti icon jadi spinner
                icon.classList.add('hidden');
                loading.classList.remove('hidden');
                text.textContent = 'Sedang Menganalisis...';

                // Opsional: Matikan tombol setelah jeda super singkat biar submit jalan dulu
                setTimeout(() => {
                    btn.disabled = true;
                }, 10);
            }
        });
    </script>

    {{-- Include Modal Detail --}}
    @include('assistantai.pages.components.detail')
@endsection

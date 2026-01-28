{{-- MODAL DETAIL (Hidden by default) --}}
<div id="detailModal" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog"
    aria-modal="true">
    {{-- Backdrop (Gelap) --}}
    <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" onclick="closeModal()"></div>

    <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
        {{-- Card Modal --}}
        <div
            class="relative transform overflow-hidden rounded-lg bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-3xl dark:bg-neutral-800">

            {{-- Header Modal --}}
            <div
                class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4 dark:bg-neutral-800 border-b dark:border-neutral-700 flex justify-between items-center">
                <h3 class="text-lg font-semibold leading-6 text-gray-900 dark:text-white" id="modalTitle">
                    Detail Dokumen
                </h3>
                <button type="button" onclick="closeModal()" class="text-gray-400 hover:text-gray-500">
                    <span class="sr-only">Close</span>
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            {{-- Body Modal (Isi Konten) --}}
            <div class="bg-white px-4 pt-5 pb-4 sm:p-6 dark:bg-neutral-800">
                <div class="mb-4">
                    <span id="modalCategory"
                        class="inline-flex items-center rounded-md bg-blue-50 px-2 py-1 text-xs font-medium text-blue-700 ring-1 ring-inset ring-blue-700/10">
                        Kategori
                    </span>
                    <span id="modalStatus"
                        class="ml-2 inline-flex items-center rounded-md px-2 py-1 text-xs font-medium">
                        Status
                    </span>
                </div>

                {{-- Area Hasil AI (Markdown Rendered) --}}
                <div class="prose prose-sm max-w-none text-gray-700 dark:text-gray-300 bg-gray-50 dark:bg-neutral-900 p-4 rounded-lg border border-gray-200 dark:border-neutral-700 overflow-y-auto max-h-[60vh]"
                    id="modalContent">
                </div>
            </div>

            {{-- Footer Modal --}}
            <div class="bg-gray-50 px-4 py-3 sm:flex sm:flex-row-reverse sm:px-6 dark:bg-neutral-900">
                <button type="button" onclick="closeModal()"
                    class="mt-3 inline-flex w-full justify-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50 sm:mt-0 sm:w-auto dark:bg-neutral-800 dark:text-white dark:ring-neutral-700 dark:hover:bg-neutral-700">
                    Tutup
                </button>
            </div>
        </div>
    </div>
</div>
<script>
    function openModal(title, category, status, contentId) {
        // 1. Ambil elemen Modal
        const modal = document.getElementById('detailModal');

        // 2. Isi Data ke dalam Modal
        document.getElementById('modalTitle').textContent = title;
        document.getElementById('modalCategory').textContent = category.toUpperCase().replace('_', ' ');

        // Atur warna badge status
        const statusBadge = document.getElementById('modalStatus');
        statusBadge.textContent = status === 'ditemukan' ? 'Ditemukan' : (status === 'tidak_ditemukan' ?
            'Tidak Ditemukan' : 'Pending');

        // Reset class warna
        statusBadge.className = "ml-2 inline-flex items-center rounded-md px-2 py-1 text-xs font-medium";
        if (status === 'ditemukan') {
            statusBadge.classList.add('bg-teal-100', 'text-teal-800', 'ring-1', 'ring-teal-600/20');
        } else if (status === 'tidak_ditemukan') {
            statusBadge.classList.add('bg-red-100', 'text-red-800', 'ring-1', 'ring-red-600/20');
        } else {
            statusBadge.classList.add('bg-gray-100', 'text-gray-800', 'ring-1', 'ring-gray-600/20');
        }

        // 3. Ambil konten HTML dari div tersembunyi dan masukkan ke body modal
        const contentSource = document.getElementById(contentId).innerHTML;
        document.getElementById('modalContent').innerHTML = contentSource;

        // 4. Munculkan Modal
        modal.classList.remove('hidden');
    }

    function closeModal() {
        const modal = document.getElementById('detailModal');
        modal.classList.add('hidden');
    }

    // Close modal on ESC key
    document.addEventListener('keydown', function(event) {
        if (event.key === "Escape") {
            closeModal();
        }
    });
</script>

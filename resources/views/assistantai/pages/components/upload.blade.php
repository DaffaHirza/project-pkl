<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
    <div class="lg:col-span-1 flex flex-col h-full">
        <label class="block text-sm font-medium text-gray-700 mb-2">
            Laporan Utama <span class="text-red-500">*</span>
        </label>

        <div class="relative w-full flex-grow min-h-[300px] h-full">
            <input id="main-upload" name="files[laporan_utama]" type="file" class="hidden" accept=".pdf"
                onchange="handleFileSelect(this, 'main')" />

            <label for="main-upload" id="main-default"
                class="flex flex-col justify-center items-center w-full h-full p-6 border-2 border-dashed border-gray-300 rounded-2xl bg-gray-50 hover:bg-blue-50 hover:border-blue-500 transition-all cursor-pointer absolute inset-0">
                <div class="w-16 h-16 bg-blue-100 text-blue-600 rounded-full flex items-center justify-center mb-4">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12">
                        </path>
                    </svg>
                </div>
                <p class="text-sm font-semibold text-gray-700">Klik untuk upload Laporan</p>
                <p class="text-xs text-gray-400 mt-1">PDF, DOCX, PNG, IMG, XLSX (Max 15MB)</p>
            </label>

            <div id="main-preview"
                class="hidden absolute inset-0 w-full h-full bg-white border-2 border-blue-500 rounded-2xl flex-col items-center justify-center p-4 shadow-sm z-10">
                <button type="button" onclick="removeFile('main')"
                    class="absolute top-3 right-3 text-gray-400 hover:text-red-500 hover:bg-red-50 rounded-full p-1 transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12">
                        </path>
                    </svg>
                </button>
                <div class="w-20 h-20 bg-blue-50 text-blue-600 rounded-xl flex items-center justify-center mb-3">
                    <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                        </path>
                    </svg>
                </div>
                <p class="text-sm font-bold text-gray-800 text-center px-4 break-all" id="main-filename">
                    filename.pdf
                </p>
                <p class="text-xs text-green-600 font-medium mt-1 bg-green-50 px-2 py-0.5 rounded-full">Siap
                    Dicek</p>
            </div>
        </div>
    </div>

    <div class="lg:col-span-2">
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 h-full">

            <div class="flex flex-col h-full">
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Proposal <span class="text-red-500">*</span>
                </label>
                <div class="relative w-full h-32 sm:h-full min-h-[140px]">
                    <input id="upload-proposal" name="files[proposal]" type="file" class="hidden"
                        onchange="handleFileSelect(this, 'proposal')" accept=".pdf,.docx,.doc" />

                    <label for="upload-proposal" id="default-proposal"
                        class="flex flex-col justify-center items-center w-full h-full border-2 border-dashed border-gray-300 rounded-xl bg-white hover:bg-gray-50 hover:border-gray-400 transition-all cursor-pointer absolute inset-0">
                        <svg class="w-6 h-6 text-gray-400 mb-1" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4">
                            </path>
                        </svg>
                        <span class="text-xs font-medium text-gray-500">Pilih File Proposal</span>
                    </label>

                    <div id="preview-proposal"
                        class="hidden absolute inset-0 w-full h-full bg-blue-50 border border-blue-200 rounded-xl flex flex-col items-center justify-center p-2 z-10">
                        <button type="button" onclick="removeFile('proposal')"
                            class="absolute top-2 right-2 text-gray-400 hover:text-red-500 hover:bg-red-100 rounded-full p-0.5 transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12">
                                </path>
                            </svg>
                        </button>
                        <div
                            class="w-10 h-10 bg-white text-blue-500 rounded-lg shadow-sm flex items-center justify-center mb-2">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                                </path>
                            </svg>
                        </div>
                        <p class="text-xs font-bold text-gray-700 text-center w-full truncate px-2"
                            id="filename-proposal">
                            file.pdf
                        </p>
                    </div>
                </div>
            </div>

            <div class="flex flex-col h-full">
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Kertas Kerja <span class="text-red-500">*</span>
                </label>
                <div class="relative w-full h-32 sm:h-full min-h-[140px]">
                    <input id="upload-kertas_kerja" name="files[kertas_kerja]" type="file" class="hidden"
                        onchange="handleFileSelect(this, 'kertas_kerja')" accept=".pdf,.docx,.doc" />

                    <label for="upload-kertas_kerja" id="default-kertas_kerja"
                        class="flex flex-col justify-center items-center w-full h-full border-2 border-dashed border-gray-300 rounded-xl bg-white hover:bg-gray-50 hover:border-gray-400 transition-all cursor-pointer absolute inset-0">
                        <svg class="w-6 h-6 text-gray-400 mb-1" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4">
                            </path>
                        </svg>
                        <span class="text-xs font-medium text-gray-500">Pilih File Kertas Kerja</span>
                    </label>

                    <div id="preview-kertas_kerja"
                        class="hidden absolute inset-0 w-full h-full bg-blue-50 border border-blue-200 rounded-xl flex flex-col items-center justify-center p-2 z-10">
                        <button type="button" onclick="removeFile('kertas_kerja')"
                            class="absolute top-2 right-2 text-gray-400 hover:text-red-500 hover:bg-red-100 rounded-full p-0.5 transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12">
                                </path>
                            </svg>
                        </button>
                        <div
                            class="w-10 h-10 bg-white text-blue-500 rounded-lg shadow-sm flex items-center justify-center mb-2">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                                </path>
                            </svg>
                        </div>
                        <p class="text-xs font-bold text-gray-700 text-center w-full truncate px-2"
                            id="filename-kertas_kerja">
                            file.pdf
                        </p>
                    </div>
                </div>
            </div>

            <div class="flex flex-col h-full">
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Resume <span class="text-red-500">*</span>
                </label>
                <div class="relative w-full h-32 sm:h-full min-h-[140px]">
                    <input id="upload-resume" name="files[resume]" type="file" class="hidden"
                        onchange="handleFileSelect(this, 'resume')" accept=".pdf,.docx,.doc" />

                    <label for="upload-resume" id="default-resume"
                        class="flex flex-col justify-center items-center w-full h-full border-2 border-dashed border-gray-300 rounded-xl bg-white hover:bg-gray-50 hover:border-gray-400 transition-all cursor-pointer absolute inset-0">
                        <svg class="w-6 h-6 text-gray-400 mb-1" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4">
                            </path>
                        </svg>
                        <span class="text-xs font-medium text-gray-500">Pilih File Resume</span>
                    </label>

                    <div id="preview-resume"
                        class="hidden absolute inset-0 w-full h-full bg-blue-50 border border-blue-200 rounded-xl flex flex-col items-center justify-center p-2 z-10">
                        <button type="button" onclick="removeFile('resume')"
                            class="absolute top-2 right-2 text-gray-400 hover:text-red-500 hover:bg-red-100 rounded-full p-0.5 transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                        <div
                            class="w-10 h-10 bg-white text-blue-500 rounded-lg shadow-sm flex items-center justify-center mb-2">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                                </path>
                            </svg>
                        </div>
                        <p class="text-xs font-bold text-gray-700 text-center w-full truncate px-2"
                            id="filename-resume">
                            file.pdf
                        </p>
                    </div>
                </div>
            </div>

            <div class="flex flex-col h-full">
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Sertifikat <span class="text-red-500">*</span>
                </label>
                <div class="relative w-full h-32 sm:h-full min-h-[140px]">
                    <input id="upload-sertifikat" name="files[sertifikat]" type="file" class="hidden"
                        onchange="handleFileSelect(this, 'sertifikat')" accept=".pdf,.docx,.doc" />

                    <label for="upload-sertifikat" id="default-sertifikat"
                        class="flex flex-col justify-center items-center w-full h-full border-2 border-dashed border-gray-300 rounded-xl bg-white hover:bg-gray-50 hover:border-gray-400 transition-all cursor-pointer absolute inset-0">
                        <svg class="w-6 h-6 text-gray-400 mb-1" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4">
                            </path>
                        </svg>
                        <span class="text-xs font-medium text-gray-500">Pilih File Sertifikat</span>
                    </label>

                    <div id="preview-sertifikat"
                        class="hidden absolute inset-0 w-full h-full bg-blue-50 border border-blue-200 rounded-xl flex flex-col items-center justify-center p-2 z-10">
                        <button type="button" onclick="removeFile('sertifikat')"
                            class="absolute top-2 right-2 text-gray-400 hover:text-red-500 hover:bg-red-100 rounded-full p-0.5 transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                        <div
                            class="w-10 h-10 bg-white text-blue-500 rounded-lg shadow-sm flex items-center justify-center mb-2">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                                </path>
                            </svg>
                        </div>
                        <p class="text-xs font-bold text-gray-700 text-center w-full truncate px-2"
                            id="filename-sertifikat">
                            file.pdf
                        </p>
                    </div>
                </div>
            </div>

        </div>

    </div>
</div>

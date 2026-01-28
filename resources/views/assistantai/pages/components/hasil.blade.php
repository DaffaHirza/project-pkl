@php
    $hasil = session('hasil_ai');
    $tanggalAnalisis = $hasil?->documentItems->first()?->created_at ?? ($hasil?->created_at ?? null);
@endphp

@if ($hasil)
    {{-- Page Header AI --}}
    <div class="w-full flex items-center py-5 justify-between dark:border-gray-700">
        <div class="flex flex-col">
            <h1 class="text-gray-dark dark:text-white text-2xl font-semibold">Hasil Analisis Dokumen</h1>
            <span class="text-xs text-gray-400">Analisis dilakukan pada:
                {{ $tanggalAnalisis?->format('Y-m-d H:i:s') ?? '-' }}</span>
        </div>
    </div>
    {{-- End Page Header AI --}}
    {{-- Card AI --}}
    <div
        class="w-full mx-auto bg-white border border-gray-200 rounded-xl shadow-2xs overflow-hidden dark:bg-neutral-900 dark:border-neutral-700">
        <div class=" bg-white rounded-xl shadow-sm">
            {{-- Table Section --}}
            <table class="min-w-full divide-y divide-gray-200 dark:divide-neutral-700">
                <thead class="bg-gray-50 dark:bg-neutral-800">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-start">
                            <div class="flex items-center gap-x-2">
                                <span class="text-sm font-semibold uppercase text-gray-800 dark:text-neutral-200">
                                    Nama Dokumen
                                </span>
                            </div>
                        </th>
                        <th scope="col" class="px-6 py-3 text-start">
                            <div class="flex items-center gap-x-2">
                                <span class="text-sm font-semibold uppercase text-gray-800 dark:text-neutral-200">
                                    Category
                                </span>
                            </div>
                        </th>

                        <th scope="col" class="px-6 py-3 text-start">
                            <div class="flex items-center gap-x-2">
                                <span class="text-sm font-semibold uppercase text-gray-800 dark:text-neutral-200">
                                    Hasil Analisis AI
                                </span>
                            </div>
                        </th>

                        <th scope="col" class="px-6 py-3 text-start">
                            <div class="flex items-center gap-x-2">
                                <span class="text-sm font-semibold uppercase text-gray-800 dark:text-neutral-200">
                                    Status
                                </span>
                            </div>
                        </th>
                        <th scope="col" class="px-6 py-3 text-start">
                            <div class="flex items-center gap-x-2">
                                <span class="text-sm font-semibold uppercase text-gray-800 dark:text-neutral-200">
                                    Aksi
                                </span>
                            </div>
                        </th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-gray-200 dark:divide-neutral-700">
                    @foreach ($hasil->documentItems as $item)
                        <tr class="bg-white hover:bg-gray-50 dark:bg-neutral-900 dark:hover:bg-neutral-800">
                            <td class="px-6 py-4 align-top">
                                <a class="block">
                                    <div class="flex text-gray-900 items-center gap-x-3">
                                        <span
                                            class="text-sm font-medium text-gray-800 dark:text-neutral-200 break-words">{{ $item->nama_file }}</span>
                                    </div>
                                </a>
                            </td>
                            <td class="px-6 py-4 align-top">
                                <a class="block">
                                    <div class="flex text-gray-900 items-center gap-x-3">
                                        <span
                                            class="text-sm font-medium text-gray-800 dark:text-neutral-200 break-words">{{ $item->kategori }}</span>
                                    </div>
                                </a>
                            </td>
                            <td class="px-6 py-4 align-top max-w-xs">
                                <a class="block">
                                    <span class="block text-sm text-gray-500 dark:text-neutral-500 break-words">
                                        {{ Str::limit($item->hasil_ai ?? '-', 100, '...') }}</span>
                                </a>
                            </td>
                            <td class="size-px whitespace-nowrap align-top">
                                <a class="block p-6">
                                    @if ($item->status_verifikasi === 'ditemukan')
                                        <span
                                            class="py-1 px-1.5 inline-flex items-center gap-x-1 text-xs font-medium bg-teal-100 text-teal-800 rounded-full dark:bg-teal-500/10 dark:text-teal-500">
                                            Ditemukan
                                        </span>
                                    @elseif ($item->status_verifikasi === 'tidak_ditemukan')
                                        <span
                                            class="py-1 px-1.5 inline-flex items-center gap-x-1 text-xs font-medium bg-red-100 text-red-800 rounded-full dark:bg-red-500/10 dark:text-red-500">
                                            Tidak Ditemukan
                                        </span>
                                    @else
                                        <span
                                            class="py-1 px-1.5 inline-flex items-center gap-x-1 text-xs font-medium bg-yellow-100 text-yellow-800 rounded-full dark:bg-yellow-500/10 dark:text-yellow-500">
                                            Pending
                                        </span>
                                    @endif
                                </a>
                            </td>
                            <td class="size-px whitespace-nowrap align-top">
                                <div class="block p-6">
                                    <button type="button"
                                        onclick="openModal('{{ addslashes($item->nama_file) }}', '{{ $item->kategori }}', '{{ $item->status_verifikasi }}', 'content-{{ $item->id }}')"
                                        class="py-2 px-3 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 transition-colors cursor-pointer">
                                        Detail
                                    </button>

                                    {{-- Hidden content untuk modal --}}
                                    <div id="content-{{ $item->id }}" class="hidden">
                                        @if ($item->hasil_ai)
                                            {!! Str::markdown($item->hasil_ai) !!}
                                        @else
                                            <p class="text-gray-400 italic">Belum ada hasil analisis.</p>
                                        @endif
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            {{-- End Table --}}
        </div>

    </div>
    <div class="grid grid-cols-1 lg:grid-cols-4 gap-6 py-6">
        <div class="bg-white rounded-lg shadow-md p-6 flex flex-col items-center justify-between">
            <h3 class="text-gray-600 text-sm font-medium mb-4 uppercase tracking-wide">Tingkat Kecocokan</h3>
            <div class="flex justify-center mb-4">
                <div class="relative w-32 h-32">
                    <svg class="transform -rotate-90 w-32 h-32">
                        <circle cx="64" cy="64" r="56" stroke="#e5e7eb" stroke-width="12"
                            fill="none" />
                        <circle cx="64" cy="64" r="56" stroke="#3b82f6" stroke-width="12" fill="none"
                            stroke-dasharray="351.86" stroke-dashoffset="52.78"
                            class="transition-all duration-1000 ease-out" />
                    </svg>
                    <div class="absolute inset-0 flex items-center justify-center">
                        <span class="text-3xl font-bold text-gray-800">{{ $hasil ? $hasil->skor : '0' }}%</span>
                    </div>
                </div>
            </div>

            <div class="text-center">
                <p class="text-green-600 font-semibold text-lg">
                    {{ $hasil ? $hasil->status_label : 'Belum Dianalisis' }}
                </p>
            </div>
        </div>

        {{-- AI Detailed Analysis Card --}}
        <div class="bg-white rounded-lg shadow-md p-6 lg:col-span-3">
            <div class="flex items-center gap-2 mb-4">
                <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z" />
                </svg>
                <h3 class="text-blue-600 font-semibold text-lg">Analisis Detail AI</h3>
            </div>

            <div class="space-y-4 text-gray-700 text-sm">
                <p>
                    {{ $hasil->kesimpulan ?? 'Belum ada dokumen yang di analisis' }}
                </p>
            </div>

            <div class="flex gap-3 mt-6">
                <button
                    class="flex items-center gap-2 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    Export Analysis PDF
                </button>

                <button
                    class="flex items-center gap-2 px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z" />
                    </svg>
                    Copy to Clipboard
                </button>
            </div>
        </div>
        {{-- End AI Detailed Analysis Card --}}
    </div>
    {{-- End Card AI --}}
@else
    <div
        class="w-full mt-6 px-6 py-8 bg-white rounded-lg border border-gray-200 dark:bg-neutral-900 dark:border-neutral-700 text-center">
        <p class="text-gray-500 dark:text-neutral-400">Belum ada hasil analisis. Silahkan upload dokumen dan klik
            "Analisis Dokumen".</p>
    </div>
@endif

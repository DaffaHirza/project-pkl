@extends('layouts.app')

@section('title', 'AI Assistant')

@section('content')

    <!-- Page Header -->
    <div class="flex items-center justify-between pb-5">
        <h1 class="text-2xl font-semibold text-gray-900 dark:text-white">AI Assistant</h1>
        <div class="relative inline-block">
            <a href="{{ route('assistantai.pages.create') }}"
                class="py-2 px-4 text-white inline-flex items-center gap-x-2 text-sm font-medium rounded-lg bg-brand-500 hover:bg-brand-600 disabled:opacity-50 disabled:pointer-events-none "
                aria-haspopup="menu" aria-expanded="false" aria-label="Dropdown">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M9 13L15 13" stroke="#FFFFFF" stroke-width="2" stroke-linecap="round" />
                    <path d="M9 9L13 9" stroke="#FFFFFF" stroke-width="2" stroke-linecap="round" />
                    <path d="M9 17L13 17" stroke="#FFFFFF" stroke-width="2" stroke-linecap="round" />
                    <path
                        d="M19 13V15C19 17.8284 19 19.2426 18.1213 20.1213C17.2426 21 15.8284 21 13 21H11C8.17157 21 6.75736 21 5.87868 20.1213C5 19.2426 5 17.8284 5 15V9C5 6.17157 5 4.75736 5.87868 3.87868C6.75736 3 8.17157 3 11 3"
                        stroke="#FFFFFF" stroke-width="2" />
                    <path d="M18 3L18 9" stroke="#FFFFFF" stroke-width="2" stroke-linecap="round" />
                    <path d="M21 6L15 6" stroke="#FFFFFF" stroke-width="2" stroke-linecap="round" />
                </svg>
                Cek Document
            </a>
        </div>
    </div>
    <!-- Table Section -->
    <div class="w-full mx-auto">
        <!-- Card -->
        <div class="flex flex-col">
            <div class="-m-1.5 overflow-x-auto">
                <div class="min-w-full inline-block align-middle">
                    <div
                        class="bg-white border border-gray-200 rounded-xl shadow-2xs overflow-hidden dark:bg-neutral-900 dark:border-neutral-700">

                        <!-- Table -->
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-neutral-700">
                            <thead class="bg-gray-50 dark:bg-neutral-800">
                                <tr>
                                    <th scope="col" class="px-6 py-5 text-start">
                                        <div class="flex items-center gap-x-2">
                                            <span
                                                class="text-sm font-semibold uppercase text-gray-800 dark:text-neutral-200">
                                                No
                                            </span>
                                        </div>
                                    </th>

                                    <th scope="col" class="px-6 py-3 text-start">
                                        <div class="flex items-center gap-x-2">
                                            <span
                                                class="text-sm font-semibold uppercase text-gray-800 dark:text-neutral-200">
                                                Judul Laporan
                                            </span>
                                        </div>
                                    </th>

                                    <th scope="col" class="px-6 py-3 text-start">
                                        <div class="flex items-center gap-x-2">
                                            <span
                                                class="text-sm font-semibold uppercase text-gray-800 dark:text-neutral-200">
                                                Review
                                            </span>
                                        </div>
                                    </th>

                                    <th scope="col" class="px-6 py-3 text-start">
                                        <div class="flex items-center gap-x-2">
                                            <span
                                                class="text-sm font-semibold uppercase text-gray-800 dark:text-neutral-200">
                                                Tanggal
                                            </span>
                                        </div>
                                    </th>

                                    <th scope="col" class="px-6 py-3 text-start">
                                        <div class="flex items-center gap-x-2">
                                            <span
                                                class="text-sm font-semibold uppercase text-gray-800 dark:text-neutral-200">
                                                Status
                                            </span>
                                        </div>
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-start">
                                        <div class="flex items-center gap-x-2">
                                            <span
                                                class="text-sm font-semibold uppercase text-gray-800 dark:text-neutral-200">
                                                Aksi
                                            </span>
                                        </div>
                                    </th>
                                </tr>
                            </thead>

                            <tbody class="divide-y divide-gray-200 dark:divide-neutral-700">
                                @forelse ($documents as $document)
                                    <tr class="bg-white hover:bg-gray-50 dark:bg-neutral-900 dark:hover:bg-neutral-800">
                                        <td class="px-6 py-4 whitespace-nowrap align-top">
                                            <a class="block">
                                                <div class="flex items-center gap-x-4">
                                                    <div>
                                                        <span
                                                            class="block text-sm font-semibold text-gray-800 dark:text-neutral-200">{{ $document->id }}</span>
                                                    </div>
                                                </div>
                                            </a>
                                        </td>
                                        <td class="px-6 py-4 align-top max-w-sm">
                                            <a class="block">
                                                <div class="flex text-gray-900 items-center gap-x-3">
                                                    <span
                                                        class="text-sm font-medium text-gray-800 dark:text-neutral-200 break-words">{{ $document->judul }}</span>
                                                </div>
                                            </a>
                                        </td>
                                        <td class="px-6 py-4 align-top max-w-lg">
                                            <a class="block">
                                                <span class="block text-sm text-gray-500 dark:text-neutral-500 break-words">
                                                    {{ Str::limit($document->kesimpulan ?? '-', 100, '...') }}</span>
                                            </a>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap align-top">
                                            <a class="block">
                                                <span
                                                    class="text-sm text-gray-600 dark:text-neutral-400">{{ $document->created_at->format('d M Y') }}</span>
                                            </a>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap align-top">
                                            <span
                                                class="py-1 px-3 inline-flex items-center text-xs font-medium rounded-full {{ $document->status_color }}">
                                                {{ $document->status_label }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 align-top">
                                            <div class="flex gap-2">
                                                <a href="{{ route('assistantai.pages.edit', $document->id) }}"
                                                    class="py-2 px-3 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700">
                                                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none"
                                                        xmlns="http://www.w3.org/2000/svg">
                                                        <path
                                                            d="M15.1875 5.42383C15.6118 5.46926 15.9499 5.66401 16.2188 5.86914C16.503 6.08603 16.8078 6.39374 17.1211 6.70703L17.293 6.87891C17.6063 7.1922 17.914 7.49698 18.1309 7.78125C18.3653 8.08862 18.5859 8.48644 18.5859 9C18.5859 9.51356 18.3653 9.91138 18.1309 10.2188C17.914 10.503 17.6063 10.8078 17.293 11.1211L10.0986 18.3154C9.94157 18.4725 9.73819 18.6886 9.47461 18.8379C9.21089 18.9872 8.92063 19.0506 8.70508 19.1045L6.09668 19.7559L6.09473 19.7568L6.05078 19.7676C5.90293 19.8045 5.68156 19.8628 5.4873 19.8818C5.28061 19.9021 4.82874 19.9088 4.45996 19.54C4.09118 19.1713 4.09794 18.7194 4.11816 18.5127C4.13719 18.3184 4.19546 18.0971 4.23242 17.9492L4.89551 15.2949C4.9494 15.0794 5.0128 14.7891 5.16211 14.5254C5.31138 14.2618 5.5275 14.0584 5.68457 13.9014L12.8789 6.70703C13.1922 6.39374 13.497 6.08603 13.7812 5.86914C14.0886 5.63466 14.4864 5.41406 15 5.41406L15.1875 5.42383Z"
                                                            stroke="#FFFFFF" stroke-width="2" />
                                                        <path d="M12.5 7.5L15.5 5.5L18.5 8.5L16.5 11.5L12.5 7.5Z"
                                                            fill="#FFFFFF" />
                                                    </svg>
                                                </a>
                                                <button
                                                    class="py-2 px-3 text-sm font-medium text-white bg-red-500 rounded-lg hover:bg-red-700">
                                                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none"
                                                        xmlns="http://www.w3.org/2000/svg">
                                                        <path d="M10 15L10 12" stroke="#FFFFFF" stroke-width="2"
                                                            stroke-linecap="round" />
                                                        <path d="M14 15L14 12" stroke="#FFFFFF" stroke-width="2"
                                                            stroke-linecap="round" />
                                                        <path
                                                            d="M3 7H21C20.0681 7 19.6022 7 19.2346 7.15224C18.7446 7.35523 18.3552 7.74458 18.1522 8.23463C18 8.60218 18 9.06812 18 10V16C18 17.8856 18 18.8284 17.4142 19.4142C16.8284 20 15.8856 20 14 20H10C8.11438 20 7.17157 20 6.58579 19.4142C6 18.8284 6 17.8856 6 16V10C6 9.06812 6 8.60218 5.84776 8.23463C5.64477 7.74458 5.25542 7.35523 4.76537 7.15224C4.39782 7 3.93188 7 3 7Z"
                                                            stroke="#FFFFFF" stroke-width="2" stroke-linecap="round" />
                                                        <path
                                                            d="M10.0681 3.37059C10.1821 3.26427 10.4332 3.17033 10.7825 3.10332C11.1318 3.03632 11.5597 3 12 3C12.4403 3 12.8682 3.03632 13.2175 3.10332C13.5668 3.17033 13.8179 3.26427 13.9319 3.37059"
                                                            stroke="#FFFFFF" stroke-width="2" stroke-linecap="round" />
                                                    </svg>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="px-6 py-4 whitespace-nowrap text-center">
                                            <span class="text-sm font-medium text-gray-500 dark:text-neutral-400">Tidak ada
                                                Document</span>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                        <!-- End Table -->

                        <!-- Footer -->
                        <div
                            class="px-6 py-4 grid gap-3 md:flex md:justify-between md:items-center border-t border-gray-200 dark:border-neutral-700">
                            <div class="max-w-sm space-y-3">
                                <select
                                    class="py-2 px-3 pe-9 block text-gray-dark border-gray-200 rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-neutral-900 dark:border-neutral-700 dark:text-neutral-400">
                                    <option>1</option>
                                    <option>2</option>
                                    <option>3</option>
                                    <option>4</option>
                                    <option selected>5</option>
                                    <option>6</option>
                                </select>
                            </div>

                            <div>
                                <div class="inline-flex gap-x-2">
                                    <button type="button"
                                        class="py-2 px-3 inline-flex items-center gap-x-2 text-sm font-medium rounded-lg border border-gray-200 bg-white text-gray-dark shadow-2xs hover:bg-gray-50 focus:outline-hidden focus:bg-gray-50 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-800 dark:border-neutral-700 dark:text-white dark:hover:bg-neutral-700 dark:focus:bg-neutral-700">
                                        <svg class="shrink-0 size-4" xmlns="http://www.w3.org/2000/svg" width="24"
                                            height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                            stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="m15 18-6-6 6-6" />
                                        </svg>
                                        Prev
                                    </button>

                                    <button type="button"
                                        class="py-2 px-3 inline-flex items-center gap-x-2 text-sm font-medium rounded-lg border border-gray-200 bg-white text-gray-dark shadow-2xs hover:bg-gray-50 focus:outline-hidden focus:bg-gray-50 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-800 dark:border-neutral-700 dark:text-white dark:hover:bg-neutral-700 dark:focus:bg-neutral-700">
                                        Next
                                        <svg class="shrink-0 size-4" xmlns="http://www.w3.org/2000/svg" width="24"
                                            height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                            stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="m9 18 6-6-6-6" />
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        </div>
                        <!-- End Footer -->
                    </div>
                </div>
            </div>
        </div>
        <!-- End Card -->
    </div>
    <!-- End Table Section -->
    </div>
@endsection

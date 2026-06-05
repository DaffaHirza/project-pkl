@extends('layouts.app')

@section('title', 'Daftar Perusahaan')

@section('content')
    <div>
        {{-- Header --}}
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
            <div>
                <a href="{{ route('kanban.clients.index') }}"
                    class="inline-flex items-center text-sm text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300 mb-2">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                    Kembali
                </a>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Daftar Perusahaan</h1>
                <p class="text-gray-600 dark:text-gray-400 mt-1">Terdiri dari Bank dan PT/CV Induk</p>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('kanban.clients.create.bank') }}"
                    class="inline-flex items-center gap-2 px-4 py-2.5 bg-blue-500 hover:bg-blue-600 text-white rounded-lg font-medium text-sm transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Tambah Bank
                </a>
                <a href="{{ route('kanban.clients.create.perusahaan-induk') }}"
                    class="inline-flex items-center gap-2 px-4 py-2.5 bg-purple-500 hover:bg-purple-600 text-white rounded-lg font-medium text-sm transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Tambah PT Induk
                </a>
            </div>
        </div>

        {{-- Alert Messages --}}
        @if (session('success'))
            <div class="mb-6 p-4 rounded-lg bg-green-50 border border-green-200 dark:bg-green-900/20 dark:border-green-800">
                <p class="text-green-700 dark:text-green-400">{{ session('success') }}</p>
            </div>
        @endif

        @if (session('error'))
            <div class="mb-6 p-4 rounded-lg bg-red-50 border border-red-200 dark:bg-red-900/20 dark:border-red-800">
                <p class="text-red-700 dark:text-red-400">{{ session('error') }}</p>
            </div>
        @endif

        {{-- Filters & Search --}}
        <div class="bg-white dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-800 p-4 mb-6">
            <form action="{{ route('kanban.clients.perusahaan') }}" method="GET" class="flex flex-col sm:flex-row gap-4">
                <div class="flex-1">
                    <input type="text" name="search" value="{{ request('search') }}"
                        placeholder="Cari nama perusahaan, SPK..."
                        class="w-full px-4 py-2 rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-brand-500 focus:border-transparent">
                </div>
                <select name="type"
                    class="px-4 py-2 rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-brand-500">
                    <option value="">Semua Tipe</option>
                    <option value="bank" {{ request('type') === 'bank' ? 'selected' : '' }}>Bank</option>
                    <option value="pt_cv" {{ request('type') === 'pt_cv' ? 'selected' : '' }}>PT/CV Induk</option>
                </select>
                <button type="submit"
                    class="px-4 py-2 bg-gray-100 hover:bg-gray-200 dark:bg-gray-800 dark:hover:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-lg transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </button>
                @if (request('search') || request('type'))
                    <a href="{{ route('kanban.clients.perusahaan') }}"
                        class="px-4 py-2 text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white transition">Reset</a>
                @endif
            </form>
        </div>

        {{-- Client Table --}}
        <div class="bg-white dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-800 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50 dark:bg-gray-800">
                        <tr>
                            <th
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                Perusahaan</th>
                            <th
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                Tipe</th>
                            <th
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                No SPK</th>
                            <th
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                Debitur/Anak</th>
                            <th
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                Asset</th>
                            <th
                                class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-800">
                        @forelse($clients as $client)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/50 transition">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center gap-3">
                                        <div
                                            class="w-10 h-10 rounded-full {{ $client->type === 'bank' ? 'bg-blue-100 dark:bg-blue-900/30' : 'bg-purple-100 dark:bg-purple-900/30' }} flex items-center justify-center flex-shrink-0">
                                            <span
                                                class="text-sm font-semibold {{ $client->type === 'bank' ? 'text-blue-600 dark:text-blue-400' : 'text-purple-600 dark:text-purple-400' }}">
                                                {{ strtoupper(substr($client->company_name ?? $client->name, 0, 2)) }}
                                            </span>
                                        </div>
                                        <div>
                                            <a href="{{ route('kanban.clients.show', $client) }}"
                                                class="font-medium text-gray-900 dark:text-white hover:text-brand-600 dark:hover:text-brand-400">
                                                {{ $client->company_name ?? $client->name }}
                                            </a>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span
                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $client->type === 'bank' ? 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400' : 'bg-purple-100 text-purple-800 dark:bg-purple-900/30 dark:text-purple-400' }}">
                                        {{ $client->type === 'bank' ? 'Bank' : 'PT/CV' }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                    {{ $client->spk_number ?? '-' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span
                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $client->children_count > 0 ? 'bg-orange-100 text-orange-800 dark:bg-orange-900/30 dark:text-orange-400' : 'bg-gray-100 text-gray-800 dark:bg-gray-800 dark:text-gray-400' }}">
                                        {{ $client->children_count ?? 0 }}
                                        {{ $client->type === 'bank' ? 'debitur' : 'anak' }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span
                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $client->assets_count > 0 ? 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400' : 'bg-gray-100 text-gray-800 dark:bg-gray-800 dark:text-gray-400' }}">
                                        {{ $client->assets_count ?? 0 }} asset
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        <a href="{{ route('kanban.clients.show', $client) }}"
                                            class="p-2 text-gray-500 hover:text-brand-600 dark:text-gray-400 dark:hover:text-brand-400 transition"
                                            title="Lihat">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                            </svg>
                                        </a>
                                        <a href="{{ route('kanban.clients.edit', $client) }}"
                                            class="p-2 text-gray-500 hover:text-blue-600 dark:text-gray-400 dark:hover:text-blue-400 transition"
                                            title="Edit">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                            </svg>
                                        </a>
                                        <form id="delete-client-form-{{ $client->id }}"
                                            action="{{ route('kanban.clients.destroy', $client) }}" method="POST"
                                            class="inline">
                                            @csrf
                                            @method('DELETE')

                                            <button type="button"
                                                onclick="openDeleteClientModal(
            @js($client->company_name ?? $client->name),
            'delete-client-form-{{ $client->id }}',
            @js($client->type === 'bank' ? 'Bank' : 'PT/CV Induk'),
            {{ $client->children_count ?? 0 }},
            {{ $client->total_assets_count ?? ($client->assets_count ?? 0) }}
        )"
                                                class="p-2 text-gray-500 hover:text-red-600 dark:text-gray-400 dark:hover:text-red-400 transition"
                                                title="Hapus">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                </svg>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-12 text-center">
                                    <div class="flex flex-col items-center">
                                        <svg class="w-12 h-12 text-gray-400 mb-4" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                        </svg>
                                        <p class="text-gray-500 dark:text-gray-400">Belum ada perusahaan terdaftar</p>
                                        <div class="flex gap-2 mt-2">
                                            <a href="{{ route('kanban.clients.create.bank') }}"
                                                class="text-blue-600 hover:text-blue-700 dark:text-blue-400 font-medium">+
                                                Tambah Bank</a>
                                            <span class="text-gray-300">|</span>
                                            <a href="{{ route('kanban.clients.create.perusahaan-induk') }}"
                                                class="text-purple-600 hover:text-purple-700 dark:text-purple-400 font-medium">+
                                                Tambah PT/CV</a>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            @if ($clients->hasPages())
                <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-800">
                    {{ $clients->withQueryString()->links() }}
                </div>
            @endif
        </div>
    </div>
    {{-- Delete Confirmation Modal --}}
    <div id="deleteClientModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/50 px-4">
        <div
            class="w-full max-w-md rounded-2xl bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 shadow-xl">
            <div class="p-6">
                <div class="flex items-start gap-4">
                    <div
                        class="flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-full bg-red-100 dark:bg-red-900/30">
                        <svg class="h-6 w-6 text-red-600 dark:text-red-400" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 9v2m0 4h.01M4.93 19h14.14c1.54 0 2.5-1.67 1.73-3L13.73 4c-.77-1.33-2.69-1.33-3.46 0L3.2 16c-.77 1.33.19 3 1.73 3z" />
                        </svg>
                    </div>

                    <div class="flex-1">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                            Hapus <span id="deleteClientType"></span>?
                        </h3>

                        <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                            Apakah Anda yakin ingin menghapus
                            <span id="deleteClientName" class="font-semibold text-gray-900 dark:text-white"></span>?
                        </p>

                        <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                            Sistem juga akan menghapus
                            <span id="deleteClientChildCount" class="font-semibold text-red-600 dark:text-red-400"></span>
                            data turunan dan
                            <span id="deleteClientAssetCount" class="font-semibold text-red-600 dark:text-red-400"></span>
                            asset yang terhubung.
                        </p>

                        <p class="mt-2 text-xs text-red-600 dark:text-red-400">
                            Data yang sudah dihapus tidak dapat dikembalikan.
                        </p>
                    </div>
                </div>

                <div class="mt-6 flex justify-end gap-3">
                    <button type="button" onclick="closeDeleteClientModal()"
                        class="px-4 py-2 rounded-lg border border-gray-300 dark:border-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800 transition">
                        Batal
                    </button>

                    <button type="button" onclick="confirmDeleteClient()"
                        class="px-4 py-2 rounded-lg bg-red-600 hover:bg-red-700 text-white font-medium transition">
                        Ya, Hapus
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        let selectedDeleteFormId = null;

        function openDeleteClientModal(clientName, formId, clientType, childCount, assetCount) {
            selectedDeleteFormId = formId;

            document.getElementById('deleteClientName').textContent = clientName;
            document.getElementById('deleteClientType').textContent = clientType;
            document.getElementById('deleteClientChildCount').textContent = childCount;
            document.getElementById('deleteClientAssetCount').textContent = assetCount;

            const modal = document.getElementById('deleteClientModal');

            modal.classList.remove('hidden');
            modal.classList.add('flex');
        }

        function closeDeleteClientModal() {
            selectedDeleteFormId = null;

            const modal = document.getElementById('deleteClientModal');

            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }

        function confirmDeleteClient() {
            if (selectedDeleteFormId) {
                document.getElementById(selectedDeleteFormId).submit();
            }
        }

        document.addEventListener('keydown', function(event) {
            if (event.key === 'Escape') {
                closeDeleteClientModal();
            }
        });

        document.getElementById('deleteClientModal').addEventListener('click', function(event) {
            if (event.target === this) {
                closeDeleteClientModal();
            }
        });
    </script>
@endsection

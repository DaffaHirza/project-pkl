@extends('admin.layouts.app')

@section('title', 'Edit User - ' . $user->name)

@section('content')
    <div class="space-y-6">
        <!-- Header -->
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Edit User</h1>
                <p class="mt-1 text-sm text-gray-500">Update informasi pengguna {{ $user->name }}</p>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('admin.users.index') }}"
                    class="inline-flex items-center gap-2 rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 transition hover:bg-gray-50">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                    Kembali
                </a>
            </div>
        </div>

        <!-- Form -->
        <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-800 dark:bg-gray-900">
            <!-- Info Alert -->
            <div class="mb-6 rounded-lg border border-blue-200 bg-blue-50 p-4 dark:border-blue-900/50 dark:bg-blue-950/30">
                <div class="flex gap-3">
                    <svg class="h-5 w-5 flex-shrink-0 text-blue-600 dark:text-blue-400" fill="currentColor"
                        viewBox="0 0 20 20">
                        <path fill-rule="evenodd"
                            d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z"
                            clip-rule="evenodd" />
                    </svg>
                    <div class="text-sm text-blue-700 dark:text-blue-300">
                        <p class="font-medium">Token auto-update dari documents</p>
                        <p class="mt-1">Nilai token otomatis terupdate berdasarkan token_input dan token_output dari semua
                            dokumen milik user. Anda dapat secara manual menyesuaikan jika diperlukan.</p>
                    </div>
                </div>
            </div>

            <form method="POST" action="{{ route('admin.users.update', $user) }}" class="space-y-6">
                @csrf
                @method('PUT')

                <!-- Name -->
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-200">Nama
                        Lengkap</label>
                    <input type="text" name="name" id="name" value="{{ old('name', $user->name) }}" required
                        class="mt-2 block w-full rounded-lg border border-gray-300 px-4 py-2.5 text-gray-900 focus:border-brand-500 focus:ring-brand-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white dark:focus:border-brand-500">
                    @error('name')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Email -->
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-200">Email</label>
                    <input type="email" name="email" id="email" value="{{ old('email', $user->email) }}" required
                        class="mt-2 block w-full rounded-lg border border-gray-300 px-4 py-2.5 text-gray-900 focus:border-brand-500 focus:ring-brand-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white dark:focus:border-brand-500">
                    @error('email')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Role -->
                <div>
                    <label for="role" class="block text-sm font-medium text-gray-700 dark:text-gray-200">Role</label>
                    <select name="role" id="role" required
                        class="mt-2 block w-full rounded-lg border border-gray-300 px-4 py-2.5 text-gray-900 focus:border-brand-500 focus:ring-brand-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white dark:focus:border-brand-500">
                        <option value="">-- Pilih Role --</option>
                        @foreach ($roles as $role)
                            <option value="{{ $role }}" @selected(old('role', $user->role) === $role)>
                                {{ ucfirst($role === \App\Models\User::ROLE_SUPERUSER ? 'Superuser (Developer)' : ($role === \App\Models\User::ROLE_ADMIN ? 'Admin' : 'User')) }}
                            </option>
                        @endforeach
                    </select>
                    @error('role')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Status -->
                <div>
                    <label for="is_active" class="flex items-center gap-3 cursor-pointer">
                        <input type="hidden" name="is_active" value="0">
                        <input type="checkbox" name="is_active" id="is_active" value="1" @checked(old('is_active', $user->is_active))
                            class="h-4 w-4 rounded border-gray-300 text-brand-500 focus:ring-brand-500 dark:border-gray-700">
                        <span class="text-sm font-medium text-gray-700 dark:text-gray-200">Aktifkan Akun</span>
                    </label>
                    @error('is_active')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Telegram Chat ID -->
                <div>
                    <label for="telegram_chat_id"
                        class="block text-sm font-medium text-gray-700 dark:text-gray-200">Telegram Chat ID</label>
                    <input type="text" name="telegram_chat_id" id="telegram_chat_id"
                        value="{{ old('telegram_chat_id', $user->telegram_chat_id) }}"
                        class="mt-2 block w-full rounded-lg border border-gray-300 px-4 py-2.5 text-gray-900 focus:border-brand-500 focus:ring-brand-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white dark:focus:border-brand-500"
                        placeholder="Opsional">
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Biarkan kosong jika tidak menggunakan
                        notifikasi Telegram</p>
                    @error('telegram_chat_id')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Jumlah Token -->
                <div>
                    <label for="jumlah_token" class="block text-sm font-medium text-gray-700 dark:text-gray-200">Jumlah
                        Token</label>
                    <input type="number" name="jumlah_token" id="jumlah_token"
                        value="{{ old('jumlah_token', $user->jumlah_token) }}" min="0"
                        class="mt-2 block w-full rounded-lg border border-gray-300 px-4 py-2.5 text-gray-900 focus:border-brand-500 focus:ring-brand-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white dark:focus:border-brand-500"
                        placeholder="0">
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Total token dari semua documents user (dapat
                        disesuaikan manual jika diperlukan)</p>
                    @error('jumlah_token')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Submit Button -->
                <div class="flex gap-3 border-t border-gray-200 pt-6 dark:border-gray-800">
                    <a href="{{ route('admin.users.index') }}"
                        class="inline-flex items-center justify-center rounded-lg border border-gray-300 bg-white px-6 py-2.5 text-sm font-medium text-gray-700 transition hover:bg-gray-50">
                        Batal
                    </a>
                    <button type="submit"
                        class="inline-flex items-center justify-center rounded-lg bg-brand-500 px-6 py-2.5 text-sm font-medium text-white transition hover:bg-brand-600">
                        Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection

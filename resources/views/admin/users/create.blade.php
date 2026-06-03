@extends('layouts.app')

@section('title', 'Create User')

@section('content')
    <div class="space-y-6">
        <div class="rounded-2xl bg-gradient-to-r from-brand-500 to-brand-600 p-6 text-white">
            <h1 class="text-2xl font-bold">Create User</h1>
            <p class="mt-1 text-sm text-brand-100">Tambahkan akun baru untuk user, admin, atau superuser.</p>
        </div>

        <div class="rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-gray-900">
            <form method="POST" action="{{ route('admin.users.store') }}" class="grid grid-cols-1 gap-5 md:grid-cols-2">
                @csrf

                <div>
                    <label for="name"
                        class="mb-1 block text-xs font-semibold uppercase tracking-wide text-gray-500">Nama</label>
                    <input id="name" name="name" type="text" value="{{ old('name') }}" required autofocus
                        class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm text-gray-700 focus:border-brand-400 focus:outline-none focus:ring-2 focus:ring-brand-200 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-200" />
                    @error('name')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="email"
                        class="mb-1 block text-xs font-semibold uppercase tracking-wide text-gray-500">Email</label>
                    <input id="email" name="email" type="email" value="{{ old('email') }}" required
                        class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm text-gray-700 focus:border-brand-400 focus:outline-none focus:ring-2 focus:ring-brand-200 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-200" />
                    @error('email')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="password"
                        class="mb-1 block text-xs font-semibold uppercase tracking-wide text-gray-500">Password</label>
                    <input id="password" name="password" type="password" required
                        class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm text-gray-700 focus:border-brand-400 focus:outline-none focus:ring-2 focus:ring-brand-200 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-200" />
                    @error('password')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="password_confirmation"
                        class="mb-1 block text-xs font-semibold uppercase tracking-wide text-gray-500">Konfirmasi
                        Password</label>
                    <input id="password_confirmation" name="password_confirmation" type="password" required
                        class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm text-gray-700 focus:border-brand-400 focus:outline-none focus:ring-2 focus:ring-brand-200 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-200" />
                </div>

                <div>
                    <label for="role"
                        class="mb-1 block text-xs font-semibold uppercase tracking-wide text-gray-500">Role</label>
                    <select id="role" name="role" required
                        class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm text-gray-700 focus:border-brand-400 focus:outline-none focus:ring-2 focus:ring-brand-200 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-200">
                        <option value="">Pilih role</option>
                        @foreach ($roles as $role)
                            <option value="{{ $role }}" @selected(old('role') === $role)>{{ ucfirst($role) }}</option>
                        @endforeach
                    </select>
                    @error('role')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="telegram_chat_id"
                        class="mb-1 block text-xs font-semibold uppercase tracking-wide text-gray-500">Telegram Chat
                        ID</label>
                    <input id="telegram_chat_id" name="telegram_chat_id" type="text"
                        value="{{ old('telegram_chat_id') }}"
                        class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm text-gray-700 focus:border-brand-400 focus:outline-none focus:ring-2 focus:ring-brand-200 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-200" />
                    @error('telegram_chat_id')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="jumlah_token"
                        class="mb-1 block text-xs font-semibold uppercase tracking-wide text-gray-500">Jumlah Token</label>
                    <input id="jumlah_token" name="jumlah_token" type="number" value="{{ old('jumlah_token', 0) }}"
                        min="0"
                        class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm text-gray-700 focus:border-brand-400 focus:outline-none focus:ring-2 focus:ring-brand-200 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-200" />
                    <p class="mt-1 text-xs text-gray-500">Total token dari documents (auto-update). Masukkan nilai awal
                        untuk user baru.</p>
                    @error('jumlah_token')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex items-center gap-3 md:col-span-2">
                    <label class="inline-flex items-center gap-2 text-sm text-gray-600 dark:text-gray-300">
                        <input type="checkbox" name="is_active" value="1"
                            class="h-4 w-4 rounded border-gray-300 text-brand-600 focus:ring-brand-500" checked>
                        Aktifkan akun
                    </label>
                </div>

                <div class="flex flex-col gap-3 md:col-span-2 sm:flex-row">
                    <button type="submit"
                        class="inline-flex items-center justify-center rounded-xl bg-brand-500 px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-brand-600">
                        Simpan User
                    </button>
                    <a href="{{ route('admin.users.index') }}"
                        class="inline-flex items-center justify-center rounded-xl border border-gray-300 px-5 py-2.5 text-sm font-semibold text-gray-700 transition hover:bg-gray-50 dark:border-gray-700 dark:text-gray-200 dark:hover:bg-gray-800">
                        Batal
                    </a>
                </div>
            </form>
        </div>
    </div>
@endsection

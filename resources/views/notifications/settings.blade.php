@extends('layouts.app')

@section('title', 'Pengaturan Notifikasi')

@section('content')
<div class="mx-auto max-w-2xl">
    {{-- Header --}}
    <div class="mb-6">
        <div class="flex items-center gap-4 mb-2">
            <a href="{{ route('notifications.index') }}" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
            </a>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Pengaturan Notifikasi</h1>
        </div>
        <p class="text-sm text-gray-500 dark:text-gray-400">
            Kelola preferensi notifikasi Anda
        </p>
    </div>

    <form action="{{ route('notifications.update-settings') }}" method="POST">
        @csrf

        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 divide-y divide-gray-200 dark:divide-gray-700">
            {{-- Email Notifications --}}
            <div class="p-6">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Notifikasi Email</h3>
                
                <div class="space-y-4">
                    <label class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-700 dark:text-gray-300">Ringkasan Harian</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">Kirim ringkasan aktivitas setiap hari</p>
                        </div>
                        <input type="checkbox" name="email_daily_summary" value="1" 
                               class="w-5 h-5 rounded border-gray-300 text-brand-500 focus:ring-brand-500">
                    </label>

                    <label class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-700 dark:text-gray-300">Tugas Baru</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">Kirim email saat ditugaskan ke card/project baru</p>
                        </div>
                        <input type="checkbox" name="email_new_assignment" value="1" checked
                               class="w-5 h-5 rounded border-gray-300 text-brand-500 focus:ring-brand-500">
                    </label>

                    <label class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-700 dark:text-gray-300">Deadline Mendekat</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">Kirim pengingat deadline 1 hari sebelumnya</p>
                        </div>
                        <input type="checkbox" name="email_deadline_reminder" value="1" checked
                               class="w-5 h-5 rounded border-gray-300 text-brand-500 focus:ring-brand-500">
                    </label>

                    <label class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-700 dark:text-gray-300">Invoice & Pembayaran</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">Kirim notifikasi terkait invoice</p>
                        </div>
                        <input type="checkbox" name="email_invoice" value="1" checked
                               class="w-5 h-5 rounded border-gray-300 text-brand-500 focus:ring-brand-500">
                    </label>
                </div>
            </div>

            {{-- In-App Notifications --}}
            <div class="p-6">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Notifikasi Dalam Aplikasi</h3>
                
                <div class="space-y-4">
                    @foreach($types as $key => $label)
                    <label class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-700 dark:text-gray-300">{{ $label }}</p>
                        </div>
                        <input type="checkbox" name="notify_{{ $key }}" value="1" checked
                               class="w-5 h-5 rounded border-gray-300 text-brand-500 focus:ring-brand-500">
                    </label>
                    @endforeach
                </div>
            </div>

            {{-- Quiet Hours --}}
            <div class="p-6">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Jam Tenang</h3>
                <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">
                    Jangan tampilkan notifikasi pada jam-jam tertentu
                </p>
                
                <div class="flex items-center gap-4">
                    <label class="flex items-center gap-2">
                        <input type="checkbox" name="quiet_hours_enabled" value="1"
                               class="w-5 h-5 rounded border-gray-300 text-brand-500 focus:ring-brand-500">
                        <span class="text-sm text-gray-700 dark:text-gray-300">Aktifkan jam tenang</span>
                    </label>
                </div>

                <div class="mt-4 flex items-center gap-4">
                    <div>
                        <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1">Dari</label>
                        <input type="time" name="quiet_hours_start" value="22:00"
                               class="text-sm rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-800">
                    </div>
                    <div>
                        <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1">Sampai</label>
                        <input type="time" name="quiet_hours_end" value="07:00"
                               class="text-sm rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-800">
                    </div>
                </div>
            </div>
        </div>

        {{-- Submit --}}
        <div class="mt-6 flex items-center justify-end gap-4">
            <a href="{{ route('notifications.index') }}" class="px-4 py-2 text-sm font-medium text-gray-700 hover:text-gray-900 dark:text-gray-300 dark:hover:text-white">
                Batal
            </a>
            <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-brand-500 rounded-lg hover:bg-brand-600 transition-colors">
                Simpan Pengaturan
            </button>
        </div>
    </form>
</div>
@endsection

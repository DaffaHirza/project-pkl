<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'KJPP Mushofah dan Rekan') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=outfit:300,400,500,600,700&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans text-gray-900 antialiased">
        <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-gradient-to-br from-brand-50 via-white to-brand-100 dark:from-gray-900 dark:via-gray-dark dark:to-gray-900">
            <!-- Logo Section -->
            <div class="mb-6">
                <a href="/" class="flex flex-col items-center gap-2 group">
                    <div class="w-16 h-16 bg-brand-500 rounded-2xl flex items-center justify-center shadow-lg shadow-brand-500/30 group-hover:shadow-brand-500/50 transition-shadow duration-300">
                        <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                        </svg>
                    </div>
                    <div class="text-center">
                        <span class="block text-xl font-semibold text-gray-800 dark:text-white">KJPP Mushofah dan Rekan</span>
                        <span class="block text-sm text-gray-500 dark:text-gray-400">Cabang Semarang</span>
                    </div>
                </a>
            </div>

            <!-- Card -->
            <div class="w-full sm:max-w-md px-8 py-8 bg-white dark:bg-gray-dark border border-gray-200 dark:border-gray-800 shadow-xl shadow-gray-200/50 dark:shadow-none rounded-2xl">
                {{ $slot }}
            </div>

            <!-- Footer -->
            <p class="mt-8 text-sm text-gray-500 dark:text-gray-400">
                &copy; {{ date('Y') }} KJPP Mushofah dan Rekan - Cabang Semarang. All rights reserved.
            </p>
        </div>
    </body>
</html>

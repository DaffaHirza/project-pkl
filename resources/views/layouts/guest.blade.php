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
                    <img src="{{ asset('images/image.png') }}" alt="Logo Kantor Jasa Penilai Publik" class="h-16 w-auto drop-shadow-lg" />
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
                &copy; {{ date('Y') }} Kantor Jasa Penilai Publik - Cabang Semarang. All rights reserved.
            </p>
        </div>
    </body>
</html>

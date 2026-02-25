<x-guest-layout>
    <!-- Header -->
    <div class="text-center mb-6">
        <div class="w-14 h-14 bg-brand-100 dark:bg-brand-500/20 rounded-full flex items-center justify-center mx-auto mb-4">
            <svg class="w-7 h-7 text-brand-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z" />
            </svg>
        </div>
        <h2 class="text-2xl font-semibold text-gray-800 dark:text-white">Forgot Password?</h2>
        <p class="text-sm text-gray-500 dark:text-gray-400 mt-2 px-4">
            No worries! Enter your email and we'll send you a reset link.
        </p>
    </div>

    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('password.email') }}">
        @csrf

        <!-- Email Address -->
        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="block mt-2 w-full" type="email" name="email" :value="old('email')" required autofocus placeholder="Enter your email address" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Submit Button -->
        <div class="mt-6">
            <x-primary-button class="w-full justify-center">
                {{ __('Send Reset Link') }}
            </x-primary-button>
        </div>

        <!-- Back to Login -->
        <p class="mt-6 text-center text-sm text-gray-600 dark:text-gray-400">
            Remember your password?
            <a href="{{ route('login') }}" class="text-brand-500 hover:text-brand-600 font-medium transition-colors">Back to sign in</a>
        </p>
    </form>
</x-guest-layout>

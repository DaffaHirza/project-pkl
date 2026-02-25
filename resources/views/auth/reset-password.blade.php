<x-guest-layout>
    <!-- Header -->
    <div class="text-center mb-6">
        <div class="w-14 h-14 bg-brand-100 dark:bg-brand-500/20 rounded-full flex items-center justify-center mx-auto mb-4">
            <svg class="w-7 h-7 text-brand-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
            </svg>
        </div>
        <h2 class="text-2xl font-semibold text-gray-800 dark:text-white">Reset Password</h2>
        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Create a new secure password</p>
    </div>

    <form method="POST" action="{{ route('password.store') }}">
        @csrf

        <!-- Password Reset Token -->
        <input type="hidden" name="token" value="{{ $request->route('token') }}">

        <!-- Email Address -->
        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="block mt-2 w-full" type="email" name="email" :value="old('email', $request->email)" required autofocus autocomplete="username" placeholder="Enter your email" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="mt-5">
            <x-input-label for="password" :value="__('New Password')" />
            <x-text-input id="password" class="block mt-2 w-full" type="password" name="password" required autocomplete="new-password" placeholder="Enter new password" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Confirm Password -->
        <div class="mt-5">
            <x-input-label for="password_confirmation" :value="__('Confirm Password')" />
            <x-text-input id="password_confirmation" class="block mt-2 w-full"
                                type="password"
                                name="password_confirmation" required autocomplete="new-password"
                                placeholder="Confirm new password" />
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <!-- Submit Button -->
        <div class="mt-6">
            <x-primary-button class="w-full justify-center">
                {{ __('Reset Password') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>

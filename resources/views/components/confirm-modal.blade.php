{{-- Confirmation Modal Component --}}
@props([
    'id' => 'confirm-modal',
    'title' => 'Konfirmasi',
    'message' => 'Apakah Anda yakin?',
    'confirmText' => 'Ya, Lanjutkan',
    'cancelText' => 'Batal',
    'type' => 'danger', // danger, warning, info
    'formAction' => null,
    'formMethod' => 'POST'
])

@php
$buttonClasses = [
    'danger' => 'btn-error',
    'warning' => 'btn-warning',
    'info' => 'btn-primary',
];

$iconColors = [
    'danger' => 'text-red-600 dark:text-red-400 bg-red-100 dark:bg-red-900/30',
    'warning' => 'text-yellow-600 dark:text-yellow-400 bg-yellow-100 dark:bg-yellow-900/30',
    'info' => 'text-blue-600 dark:text-blue-400 bg-blue-100 dark:bg-blue-900/30',
];
@endphp

<div 
    x-data="{ 
        open: false,
        action: '{{ $formAction }}',
        setAction(url) {
            this.action = url;
            this.open = true;
        }
    }"
    x-on:open-{{ $id }}.window="setAction($event.detail?.action || '{{ $formAction }}')"
    x-on:close-{{ $id }}.window="open = false"
    x-show="open"
    x-cloak
    class="fixed inset-0 z-50"
    role="dialog"
    aria-modal="true"
>
    <div 
        class="fixed inset-0 bg-black/50"
        @click="open = false"
    ></div>

    <div class="fixed inset-0 flex items-center justify-center p-4">
        <div 
            x-show="open"
            x-transition:enter="ease-out duration-300"
            x-transition:enter-start="opacity-0 scale-95"
            x-transition:enter-end="opacity-100 scale-100"
            x-transition:leave="ease-in duration-200"
            x-transition:leave-start="opacity-100 scale-100"
            x-transition:leave-end="opacity-0 scale-95"
            class="relative bg-white dark:bg-gray-800 rounded-lg shadow-xl max-w-md w-full"
        >
            <div class="p-6">
                <div class="flex items-start gap-4">
                    <div class="flex-shrink-0 w-12 h-12 rounded-full {{ $iconColors[$type] }} flex items-center justify-center">
                        @if($type === 'danger')
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                        @elseif($type === 'warning')
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        @else
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        @endif
                    </div>
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">{{ $title }}</h3>
                        <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">{{ $message }}</p>
                    </div>
                </div>

                <div class="mt-6 flex justify-end gap-3">
                    <button type="button" @click="open = false" class="btn btn-ghost">
                        {{ $cancelText }}
                    </button>
                    
                    @if($formAction)
                    <form :action="action" method="POST" class="inline">
                        @csrf
                        @if($formMethod !== 'POST')
                        @method($formMethod)
                        @endif
                        <button type="submit" class="btn {{ $buttonClasses[$type] }}">
                            {{ $confirmText }}
                        </button>
                    </form>
                    @else
                    <button type="button" @click="$dispatch('confirmed-{{ $id }}'); open = false" class="btn {{ $buttonClasses[$type] }}">
                        {{ $confirmText }}
                    </button>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

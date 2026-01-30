{{-- Alert Component --}}
@props([
    'type' => 'info', // info, success, warning, error
    'dismissible' => true,
    'title' => null
])

@php
$classes = [
    'info' => 'alert-info',
    'success' => 'alert-success',
    'warning' => 'alert-warning',
    'error' => 'alert-error',
];

$icons = [
    'info' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />',
    'success' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />',
    'warning' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />',
    'error' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />',
];
@endphp

<div x-data="{ show: true }" x-show="show" x-transition {{ $attributes->merge(['class' => "alert {$classes[$type]}"]) }}>
    <svg class="w-6 h-6 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        {!! $icons[$type] !!}
    </svg>
    <div>
        @if($title)
        <h3 class="font-bold">{{ $title }}</h3>
        @endif
        <div class="text-sm">{{ $slot }}</div>
    </div>
    @if($dismissible)
    <button @click="show = false" class="btn btn-sm btn-ghost">✕</button>
    @endif
</div>

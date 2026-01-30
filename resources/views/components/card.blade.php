{{-- Card Component --}}
@props([
    'title' => null,
    'padding' => true,
    'border' => true,
    'shadow' => false,
    'hover' => false
])

@php
$classes = 'bg-white dark:bg-gray-800 rounded-lg';

if ($border) {
    $classes .= ' border border-gray-200 dark:border-gray-700';
}

if ($shadow) {
    $classes .= ' shadow-lg';
}

if ($hover) {
    $classes .= ' hover:shadow-md transition-shadow';
}
@endphp

<div {{ $attributes->merge(['class' => $classes]) }}>
    @if($title || isset($header))
    <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
        @if(isset($header))
            {{ $header }}
        @else
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">{{ $title }}</h3>
        @endif
    </div>
    @endif

    <div @class(['p-6' => $padding])>
        {{ $slot }}
    </div>

    @if(isset($footer))
    <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800/50 rounded-b-lg">
        {{ $footer }}
    </div>
    @endif
</div>

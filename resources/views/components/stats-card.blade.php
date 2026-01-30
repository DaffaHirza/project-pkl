{{-- Stats Card Component --}}
@props([
    'title' => '',
    'value' => '0',
    'change' => null, // positive number = increase, negative = decrease, null = no change
    'changeLabel' => 'vs bulan lalu',
    'icon' => null,
    'iconBg' => 'bg-blue-100 dark:bg-blue-900/30',
    'iconColor' => 'text-blue-600 dark:text-blue-400',
    'href' => null
])

@php
$tag = $href ? 'a' : 'div';
$linkAttrs = $href ? "href=\"{$href}\"" : '';
@endphp

<{{ $tag }} {{ $linkAttrs }} {{ $attributes->merge(['class' => 'bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-6' . ($href ? ' hover:border-blue-500 dark:hover:border-blue-500 transition-colors cursor-pointer' : '')]) }}>
    <div class="flex items-start justify-between">
        <div>
            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ $title }}</p>
            <p class="text-2xl font-bold text-gray-900 dark:text-white mt-1">{{ $value }}</p>
            
            @if($change !== null)
            <div class="flex items-center gap-1 mt-2">
                @if($change > 0)
                <svg class="w-4 h-4 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M5.293 9.707a1 1 0 010-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 01-1.414 1.414L11 7.414V15a1 1 0 11-2 0V7.414L6.707 9.707a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                </svg>
                <span class="text-sm font-medium text-green-500">{{ $change }}%</span>
                @elseif($change < 0)
                <svg class="w-4 h-4 text-red-500" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M14.707 10.293a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 111.414-1.414L9 12.586V5a1 1 0 012 0v7.586l2.293-2.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                </svg>
                <span class="text-sm font-medium text-red-500">{{ abs($change) }}%</span>
                @else
                <span class="text-sm text-gray-400">0%</span>
                @endif
                <span class="text-xs text-gray-400">{{ $changeLabel }}</span>
            </div>
            @endif
        </div>

        @if($icon)
        <div class="w-12 h-12 rounded-lg {{ $iconBg }} flex items-center justify-center {{ $iconColor }}">
            {!! $icon !!}
        </div>
        @endif
    </div>
</{{ $tag }}>

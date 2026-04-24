@props([
    'id' => null,
    'name' => null,
    'placeholder' => '',
    'value' => null,
])

<input type="date" @if ($id) id="{{ $id }}" @endif
    @if ($name) name="{{ $name }}" @endif value="{{ old($name ?? '', $value) }}"
    placeholder="{{ $placeholder }}"
    {{ $attributes->merge([
        'class' =>
            'h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800',
    ]) }} />

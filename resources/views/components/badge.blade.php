{{-- Badge Component --}}
@props([
    'type' => 'default', // default, primary, secondary, accent, info, success, warning, error, ghost
    'size' => 'md', // xs, sm, md, lg
    'outline' => false
])

@php
$typeClasses = [
    'default' => '',
    'primary' => 'badge-primary',
    'secondary' => 'badge-secondary',
    'accent' => 'badge-accent',
    'info' => 'badge-info',
    'success' => 'badge-success',
    'warning' => 'badge-warning',
    'error' => 'badge-error',
    'ghost' => 'badge-ghost',
];

$sizeClasses = [
    'xs' => 'badge-xs',
    'sm' => 'badge-sm',
    'md' => '',
    'lg' => 'badge-lg',
];

$classes = 'badge ' . $typeClasses[$type] . ' ' . $sizeClasses[$size];
if ($outline) {
    $classes .= ' badge-outline';
}
@endphp

<span {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</span>

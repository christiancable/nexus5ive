@props([
    'type' => 'button',
    'variant' => 'secondary',
    'size' => 'sm',
    'disabled' => false,
])

@php
    $sizeClass = $size !== 'md' ? 'btn-' . $size : '';
    $class = $attributes->class([
        "btn",
        "btn-{$variant}",
        $sizeClass,
        "d-inline-flex align-items-center gap-1",
        $disabled ? 'disabled' : '',
    ]);
@endphp

<button 
    type="{{ $type }}" 
    {{ $class }}
    @if ($disabled) disabled @endif
>
    @isset($icon)
        <span class="me-1">
            {{ $icon }}
        </span>
    @endisset

    {{ $slot }}

    @isset($iconRight)
        <span class="ms-1">
            {{ $iconRight }}
        </span>
    @endisset
</button>

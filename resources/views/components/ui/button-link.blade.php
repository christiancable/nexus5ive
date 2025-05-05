@props([
    'href' => '#',
    'variant' => 'secondary',
    'size' => 'sm',
    'external' => false,
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

<a href="{{ $disabled ? '#' : $href }}" role="button"
   {{ $class }}
   @if($external) target="_blank" rel="noopener noreferrer" @endif
   @if ($disabled) aria-disabled="true" @endif
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
</a>

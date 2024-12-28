@props(['align' => 'right', 'width' => '48', 'contentClasses' => 'py-1 bg-white'])

@php
$alignmentClasses = match ($align) {
    'left' => 'dropdown-menu-start',
    'top' => 'dropdown-menu-top',
    default => 'dropdown-menu-end',
};

$width = match ($width) {
    '48' => 'min-w-48',
    default => $width,
};
@endphp

<div class="dropdown" x-data="{ open: false }" @click.outside="open = false" @close.stop="open = false">
    <div @click="open = ! open">
        {{ $trigger }}
    </div>

    <div x-show="open"
            x-transition:enter="fade"
            x-transition:enter-start="fade"
            x-transition:enter-end="show"
            x-transition:leave="fade"
            x-transition:leave-start="show"
            x-transition:leave-end="fade"
            class="dropdown-menu {{ $alignmentClasses }} {{ $width }} shadow-sm"
            style="display: none;"
            @click="open = false">
        <div class="dropdown-content {{ $contentClasses }}">
            {{ $content }}
        </div>
    </div>
</div>

{{-- @todo figure out user theme loading --}}
@if ($theme->external)
    @vite(['resources/sass/additional.scss'])
    <link href="{{ $theme->path }}" rel="stylesheet">
@else
    @vite([$theme->path])
@endif
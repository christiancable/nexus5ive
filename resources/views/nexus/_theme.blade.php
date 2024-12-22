{{-- @todo figure out user theme loading --}}
@if ($theme->external)
    {{-- @vite(['resources/sass/extra.scss']) --}}
    {{-- <link href="{{ $theme->path }}" rel="stylesheet"> --}}
@else
    {{-- @vite([$theme->path]) --}}
@endif
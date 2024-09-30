@if ($theme->external)
    <link href="{{ $theme->path }}" rel="stylesheet">
    @vite(['resources/sass/extra.scss'])
@else
    <link href="{{ asset($theme->path) }}" rel="stylesheet">
@endif
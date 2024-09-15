@if ($theme->external)
    <link href="{{ $theme->path }}" rel="stylesheet">
    @vite(['resources/css/extra.sass'])
@else
    <link href="{{ asset($theme->path) }}" rel="stylesheet">
@endif
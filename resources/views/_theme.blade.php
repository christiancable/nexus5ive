@if ($theme->external)
    <link href="{{ $theme->path }}" rel="stylesheet">
    <link href="{{ mix('/css/extra.css') }}" rel="stylesheet">
@else
    <link href="{{ mix($theme->path) }}" rel="stylesheet">
@endif
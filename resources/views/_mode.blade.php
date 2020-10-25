@php
    $mode  = App\Mode::active()->first();
@endphp

@auth
    @if ($mode)
        @if ($mode->override)
            @include('_theme', ['theme' => $mode->theme])
        @else
            @include('_theme', ['theme' => Auth::user()->theme]) 
        @endif
    @else
        @include('_theme', ['theme' => Auth::user()->theme]) 
    @endif
@endauth

@guest
    @if ($mode)
        @include('_theme', ['theme' => $mode->theme])
    @else 
        <link rel="stylesheet" href="{{ mix('/css/app.css') }}">
    @endif
@endguest


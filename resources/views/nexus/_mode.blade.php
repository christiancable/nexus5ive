@php
    $mode  = App\Models\Mode::active()->first();
@endphp

@auth
    @if ($mode)
        @if ($mode->override)
            @include('nexus._theme', ['theme' => $mode->theme])
        @else
            @include('nexus._theme', ['theme' => Auth::user()->theme]) 
        @endif
    @else
        @include('nexus._theme', ['theme' => Auth::user()->theme]) 
    @endif
@endauth

@guest
    @if ($mode)
        @include('nexus._theme', ['theme' => $mode->theme])
    @else 
        <link rel="stylesheet" href="{{ mix('/css/app.css') }}">
    @endif
@endguest


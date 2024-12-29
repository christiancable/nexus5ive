{{-- @todo theme support --}}
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
         @vite(['resources/sass/app.scss'])
    @endif
@endguest


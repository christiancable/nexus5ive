<!DOCTYPE html>
<html>
  <head>
    <meta charset="UTF-8"> 
    @yield('meta')

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">


    <?php
    // if we have a special event then any theme it has should override the others
    if (config('nexus.special_event') !== '') {
        $specialTheme = App\Theme::where('name', '=', config('nexus.special_event'))->first();
    } else {
        $specialTheme = false;
    }
    ?>

    @if ($specialTheme)
    <link rel="stylesheet" href="{{ mix($specialTheme->path) }}">
    @else 
        @if (config('nexus.bootstrap_theme'))
            <link rel="stylesheet" href="{{config('nexus.bootstrap_theme')}}">
            <link href="{{ mix('/css/extra.css') }}" rel="stylesheet">
            @else

            @if (Auth::check())
                <link rel="stylesheet" href="{{ mix(Auth::User()->theme->path) }}">
            @else
                <link rel="stylesheet" href="{{ mix('/css/app.css') }}">
            @endif
        @endif
    @endif 


    <link rel="apple-touch-icon" href="/apple-touch.png">
  </head>

  <body>

  @if (Auth::check())
      @include('_toolbar')
  @endif 

  @yield('breadcrumbs')

  @include('_alerts')

  @yield('content')

  @if (Auth::check())
      @include('_footer-navigation')
  @endif

  <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
  <script src="{{ mix('/js/app.js') }}"></script>
  @yield('javascript')
  <!-- Include all compiled plugins (below), or include individual files as needed -->
  
  @if (Auth::check())
    @include('javascript._toolbar')
  @endif

  @include('_googleanaytics')

  </body>
</html>

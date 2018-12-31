<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1"> 
    @yield('meta')

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

     @auth
        <script type="text/javascript">
            (function() {
                window.notificationPoll = {{config('nexus.notification_check_interval')}}
            })();
        </script>
    @endauth

    <script src="{{ mix('js/app.js') }}" defer></script>

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

            @auth
                {{-- auth users can have a theme --}}
                @if (Auth::User()->theme->external)
                    <link href="{{ Auth::User()->theme->path }}" rel="stylesheet">
                    <link href="{{ mix('/css/extra.css') }}" rel="stylesheet">
                @else 
                    <link href="{{ mix(Auth::User()->theme->path) }}" rel="stylesheet">
                @endif
            @endauth

            @guest
                <link rel="stylesheet" href="{{ mix('/css/app.css') }}">
            @endguest

        @endif
    @endif 


    <link rel="apple-touch-icon" href="/apple-touch.png">
  </head>

  <body>
    <div>
        @auth
            @include('_toolbar')
        @endauth

        @guest
            @include('_toolbar-guest')
        @endguest

        @yield('breadcrumbs')

        @include('_alerts')

        @yield('content')

        @auth
            {{-- UGLY HACK - add space at the bottom on small screens so the bottom nav does not hide content  --}}
            <div class="container mb-6 d-lg-none"> &nbsp; </div>
            <div class="container mb-6 d-lg-none"> &nbsp; </div>
            <div class="container mb-6 d-lg-none"> &nbsp; </div>
            <div class="container mb-6 d-lg-none"> &nbsp; </div>
            @include('_footer-navigation')
        @endauth

        @include('_googleanaytics')
    </div>
  </body>
</html>

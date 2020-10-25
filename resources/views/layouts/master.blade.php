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


    @include('_mode')


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
            {{-- add space at the bottom on small screens so the bottom nav does not hide content  --}}
            <div class="container footer-navigation-spacer"> &nbsp; </div>
            @include('_footer-navigation')
        @endauth

        @include('_googleanaytics')
    </div>
  </body>
</html>

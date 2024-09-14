<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1"> 
    @yield('meta')


<!-- add bootstrap via CDN -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css" integrity="sha384-xOolHFLEh07PJGoPkLv1IbcEPTNtaed2xpHsD9ESMhqIYd0nLMwNLD69Npy4HI+N" crossorigin="anonymous">

<script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/open-iconic/1.1.1/font/css/open-iconic-bootstrap.min.css" integrity="sha512-UyNhw5RNpQaCai2EdC+Js0QL4RlVmiq41DkmCJsRV3ZxipG2L0HhTqIf/H9Hp8ez2EnFlkBnjRGJU2stW3Lj+w==" crossorigin="anonymous" referrerpolicy="no-referrer" />

<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-Fy6S3B9q64WdZWQUiU+q4/2Lc9npb8tCaSX9FK7E8HnRr0Jz8D6OP9dO5Vg3Q9ct" crossorigin="anonymous"></script>

<script src="https://cdn.jsdelivr.net/npm/vue@2.5.17"></script>

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


    @include('nexus._mode')

    <link rel="apple-touch-icon" href="/apple-touch.png">
  </head>

  <body>
    <div>
        @auth
            @include('nexus._toolbar')
        @endauth

        @guest
            @include('nexus._toolbar-guest')
        @endguest

        @yield('breadcrumbs')

        @include('nexus._alerts')

        @yield('content')

        @auth
            {{-- add space at the bottom on small screens so the bottom nav does not hide content  --}}
            <div class="container footer-navigation-spacer"> &nbsp; </div>
            @include('nexus._footer-navigation')
        @endauth

        @include('nexus._googleanaytics')
    </div>
  </body>
</html>
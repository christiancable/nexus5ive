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
    
            <link rel="stylesheet" href="{{ mix('/css/app.css') }}">
            
        @endif
    @endif 


    <link rel="apple-touch-icon" href="/apple-touch.png">
  </head>

  <body>
    <div>
        
        @include('nexus._toolbar-guest', ['hideRegistration' => true])
        
        @yield('breadcrumbs')

        @include('nexus._alerts')

        @yield('content')


        @include('nexus._googleanaytics')
    </div>
  </body>
</html>

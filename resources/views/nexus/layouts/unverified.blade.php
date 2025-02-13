<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}"/>
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="apple-touch-icon" href="{{asset('/images/apple-touch.png')}}">
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"
        integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>

    @vite(['resources/sass/app.scss'])
    @livewireStyles
    @vite(['resources/js/app.js'])

    @yield('meta')
    @include('nexus._mode')

</head>

<body>
    <div>
        
        @include('nexus._toolbar-guest')
        

        @yield('breadcrumbs')

        @include('nexus._alerts')

        @yield('content')

        @include('nexus._googleanaytics')
    </div>
    @livewireScripts
</body>

</html>

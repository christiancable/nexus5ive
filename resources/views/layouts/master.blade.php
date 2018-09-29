<!DOCTYPE html>
<html>
  <head>
    <meta charset="UTF-8"> 
    @yield('meta')

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

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

    <link rel="apple-touch-icon" href="/apple-touch.png">
  </head>

  <body>

  @if (Auth::check())
    <span id="top-toolbar">
      @include('_toolbar')
    </span>
  @endif 

  @yield('breadcrumbs')

  @include('_alerts')

  @yield('content')

  @if (Auth::check())
    <nav class="navbar navbar-default navbar-fixed-bottom visible-xs">
      <div class="container">
        <ul class="nav navbar-nav">
          
          <li class="text-center col-xs-6">
            <a {!! App\Helpers\GoogleAnalyticsHelper::onClickEvent('BottomNavigation', 'Catch-Up') !!}
              href="{{ action('Nexus\SectionController@leap')}}">
              <span class="glyphicon glyphicon-circle-arrow-right" aria-hidden="true" style="vertical-align:middle"></span> Next
            </a>
          </li>

          <li class="text-center col-xs-6">
            <a {!! App\Helpers\GoogleAnalyticsHelper::onClickEvent('BottomNavigation', 'Latest') !!}
              href="{{ action('Nexus\SectionController@latest')}}">
              <span class="glyphicon glyphicon-time" aria-hidden="true" style="vertical-align:middle"></span> Latest
            </a>
          </li> 

        </ul>
      </div>
    </nav>
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

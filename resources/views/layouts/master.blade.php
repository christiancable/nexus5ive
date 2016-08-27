<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8"> 
  @yield('meta')

  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  @if (env('NEXUS_BOOTSTRAP_THEME'))
  <link rel="stylesheet" href="{{env('NEXUS_BOOTSTRAP_THEME')}}">
  @else
  <link href="/css/app.css" rel="stylesheet">
  @endif
  <link href="/css/extra.css" rel="stylesheet">
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
         <a {!! Nexus\Helpers\GoogleAnalyticsHelper::onClickEvent('BottomNavigation', 'Catch-Up') !!}
         href="{{ action('Nexus\SectionController@leap')}}">
         <span class="glyphicon glyphicon-circle-arrow-right" aria-hidden="true" style="vertical-align:middle"></span> Next</a>
       </li> 
       <li class="text-center col-xs-6">
         <a {!! Nexus\Helpers\GoogleAnalyticsHelper::onClickEvent('BottomNavigation', 'Latest') !!}
         href="{{ action('Nexus\SectionController@latest')}}">
         <span class="glyphicon glyphicon-time" aria-hidden="true" style="vertical-align:middle"></span> Latest</a>
       </li> 
     </ul>
   </div>
 </nav>
 @endif


 <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
 <script src="https://code.jquery.com/jquery.js"></script>
 @yield('javascript')
 <!-- Include all compiled plugins (below), or include individual files as needed -->
 @include('javascript._toolbar');
 <script src="//netdna.bootstrapcdn.com/bootstrap/3.1.1/js/bootstrap.min.js"></script>
 @include('_googleanaytics')

</body>
</html>
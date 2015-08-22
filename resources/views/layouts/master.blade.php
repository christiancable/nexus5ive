<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8"> 
  @yield('meta')

    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="/css/app.css" rel="stylesheet">
    <link rel="stylesheet" href="http://bootswatch.com/cerulean/bootstrap.css">
</head>
<body>


<div class="navbar navbar-default">
      <div class="container">
        <div class="navbar-header">
          <a href="/" class="navbar-brand">Home</a>
          <button class="navbar-toggle" type="button" data-toggle="collapse" data-target="#navbar-main">
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </button>
        </div>
        <div class="navbar-collapse collapse" id="navbar-main">
          <ul class="nav navbar-nav">
        <li><a href="#">Topic Leap</a></li>
        <li><a href="#">Who's Online</a></li>
        <li><a href="/users/">Examine User</a></li>      
          </ul>

          <ul>
             <button class="navbar-toggle" type="button" data-toggle="collapse" data-target="#navbar-main">
            <span class="icon-bar">1</span>
            <span class="icon-bar">2</span>
            <span class="icon-bar">3</span>
          </button>
          </ul>

        </div>
      </div>
    </div>



<div class="container">
    <ol class="breadcrumb">
        @if (Auth::check())
          @if ($authUser = Auth::user())
            <li><a href="{{ url("/users/{$authUser->nexusUser->user_name}") }}">{{$authUser->nexusUser->user_name}}</a> ({{$authUser->nexusUser->user_popname}})</li>
            {{-- @endif --}}
            <li><a href="/auth/logout">logout</a></li>
          <li class="navbar-right"><a href="#">Messages <span class="badge" id="unread_message_count">3</span></a></li>
          @endif
        @else 
          <li><a href="/auth/login">login</a></li>
        @endif
      </ol>

</div>







  @yield('content')

 <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
    <script src="https://code.jquery.com/jquery.js"></script>
    <!-- Include all compiled plugins (below), or include individual files as needed -->
    <script src="//netdna.bootstrapcdn.com/bootstrap/3.1.1/js/bootstrap.min.js"></script>

  </body>
  </html>

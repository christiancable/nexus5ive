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


  <nav class="navbar navbar-default navbar-fixed-top">
    <div class="container">
      <div class="navbar-header">
        <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
          <span class="sr-only">Toggle navigation</span>
          <span class="icon-bar"></span>
          <span class="icon-bar"></span>
          <span class="icon-bar"></span>
        </button>
        <a class="navbar-brand" href="#">NexusFive</a>
      </div>
      <div id="navbar" class="navbar-collapse collapse">

       @if (Auth::check())
          @if ($authUser = Auth::user())

            <ul class="nav navbar-nav">
              <li {{-- class="active" --}}><a href="/">Home</a></li>
              <li><a href="/users/">Examine User</a></li>
              {{-- <li><a href="#contact">Contact</a></li> --}}
    {{--           <li class="dropdown">
                <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Dropdown <span class="caret"></span></a>
                <ul class="dropdown-menu">
                  <li><a href="#">Action</a></li>
                  <li><a href="#">Another action</a></li>
                  <li><a href="#">Something else here</a></li>
                  <li role="separator" class="divider"></li>
                  <li class="dropdown-header">Nav header</li>
                  <li><a href="#">Separated link</a></li>
                  <li><a href="#">One more separated link</a></li>
                </ul>
              </li> --}}
            </ul>
            <ul class="nav navbar-nav navbar-right">
              <li><a href="/auth/logout">Logout</a></li>
              {{-- <li><a href="../navbar-static-top/">Static top</a></li> --}}
              {{-- <li class="active"><a href="./">Fixed top <span class="sr-only">(current)</span></a></li> --}}
            </ul>
        
          @endif
        
        @endif
      </div><!--/.nav-collapse -->
    </div>
  </nav>

<div class="container">
    <ol class="breadcrumb">
        @if (Auth::check())
          @if ($authUser = Auth::user())


            <li><a href="{{ action('Nexus\UserController@show', ['user_name' => $authUser->username])}}">{{$authUser->username}}</a> ({{$authUser->popname}})</li>
            {{-- @endif --}}
            <li><a href="/auth/logout">logout</a></li>
          {{-- <li class="navbar-right"><a href="#">Messages <span class="badge" id="unread_message_count">3</span></a></li> --}}
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

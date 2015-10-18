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


  <nav class="navbar navbar-default ">
    <div class="container">
      <div class="navbar-header">
        <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
          <span class="sr-only">Toggle navigation</span>
          <span class="icon-bar"></span>
          <span class="icon-bar"></span>
          <span class="icon-bar"></span>
        </button>
        <a class="navbar-brand " href="/">NexusFive</a>
      </div>
      <div id="navbar" class="navbar-collapse collapse">

       @if (Auth::check())
          @if ($authUser = Auth::user())

            <ul class="nav navbar-nav">
              <li><a href="/users/">Examine User</a></li>
               <li><a href="/leap">Topic Leap</a></li>
            </ul>
            <ul class="nav navbar-nav navbar-right">
              <li class="dropdown">
                <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">{{$authUser->username}} ({{$authUser->popname}}) <span class="caret"></span></a>
                <ul class="dropdown-menu">
                  <li><a href="{{ action('Nexus\UserController@show', ['user_name' => $authUser->username])}}">Profile</a></li>
                  <li role="separator" class="divider"></li>
                  <li><a href="{{ action('Auth\AuthController@getLogout')}}">Logout</a></li>
                </ul>
              </li>
            </ul>
  
          @endif
        
        @endif
      </div><!--/.nav-collapse -->
    </div>
  </nav>

  <div class="container">
    <ol class="breadcrumb">
      <li><a href="#">These</a></li>
      <li><a href="#">Are</a></li>
      <li><a href="#">Not</a></li>
      <li><a href="#">Real</a></li>
      <li class="active">Breadcrumbs</li>
    </ol>
  </div>


  @yield('content')

 <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
    <script src="https://code.jquery.com/jquery.js"></script>
    <!-- Include all compiled plugins (below), or include individual files as needed -->
    <script src="//netdna.bootstrapcdn.com/bootstrap/3.1.1/js/bootstrap.min.js"></script>

  </body>
  </html>

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


  <nav class="navbar navbar-default ">
    <div class="container">
      <div class="navbar-header">
        <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
          <span class="sr-only">Toggle navigation</span>
          <span class="icon-bar"></span>
          <span class="icon-bar"></span>
          <span class="icon-bar"></span>
        </button>
        <a class="navbar-brand " href="/">{{env('NEXUS_NAME')}}</a>
      </div>
      <div id="navbar" class="navbar-collapse collapse">

       @if (Auth::check())
          @if ($authUser = Auth::user())
            <?php
              $commentsCount = $authUser->newCommentCount();
              $messagesCount = $authUser->newMessageCount();
              $notificationCount = $commentsCount + $messagesCount;
            ?>
            <ul class="nav navbar-nav">
              <li><a href="/users/">Users</a></li>
               <li><a href="/leap">Catch-up</a></li> 
               <li><a href="{{ action('Nexus\ActivityController@index')}}">Who's Online</a></li>
               <li><a href="{{ action('Nexus\SectionController@latest')}}">Latest</a></li>
               <li><a href="{{ action('Nexus\SearchController@index')}}">Search</a></li>
            </ul>
            <ul class="nav navbar-nav navbar-right">
              <li class="dropdown">

                <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">
                {{$authUser->username}} ({{$authUser->popname}}) 
                  @if ($notificationCount)
                    <span class="badge">{{$notificationCount}}</span>
                  @endif
                <span class="caret"></span>
                </a>
                <ul class="dropdown-menu">
                  <li><a href="{{ action('Nexus\UserController@show', ['user_name' => $authUser->username])}}"> 
                  <span class="glyphicon glyphicon glyphicon-user" aria-hidden="true"></span> Profile 
                 @if ($commentsCount)
                    <span class="badge">{{$commentsCount}}</span>
                  @endif
                  </a></li>

                  <li><a href="{{action('Nexus\MessageController@index')}}">
                  <span class="glyphicon glyphicon glyphicon-envelope" aria-hidden="true"></span> Inbox 
                 @if ($messagesCount)
                    <span class="badge">{{$messagesCount}}</span>
                  @endif
                  </a></li>
                  @if ($authUser->sections->count())
                  <li role="separator" class="divider"></li>
                  <li class="dropdown-header">Moderator Goodies</li>
                    <li><a href="{{ action('Nexus\RestoreController@index')}}">
                    <span class="glyphicon glyphicon glyphicon glyphicon-open" aria-hidden="true"></span> Your Archive</a></li>
                   @endif
                   <li role="separator" class="divider"></li>
                    <li><a href="{{ action('Auth\AuthController@getLogout')}}">
                    <span class="glyphicon glyphicon glyphicon-log-out" aria-hidden="true"></span> Logout</a></li>
                  
                </ul>
              </li>
            </ul>
  
          @endif
        
        @endif
      </div><!--/.nav-collapse -->
    </div>
  </nav>

  @yield('breadcrumbs')
  
  @if ($message = Session::get('headerAlert'))
    <div class="container">
        <div class="alert alert-info" role="alert">
            {!! Nexus\Helpers\NxCodeHelper::nxDecode($message) !!}
        </div>
    </div>
@endif


@if (session('alert'))
    <div class="container">
        <div class="alert alert-warning" role="alert">No updated topics found. Why not start a new conversation or read more sections?</div>
    </div>
@endif 
  
@if (session('topic'))
    <div class="container">
        <div class="alert alert-success" role="alert">
        <p>People have been talking! New posts found in <strong><a href="{{ action('Nexus\TopicController@show', ['topic_id' => session('topic')->id])}}"> {{session('topic')->title}}</a></strong><p>
        <p>Seeing too many old topics then <strong><a href="{{ action('Nexus\TopicController@markAllSubscribedTopicsAsRead')}}">mark all subscribed topics as read.</a></strong></p>
        </div>
    </div>
@endif    

  
  

  @yield('content')

 <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
 <script src="https://code.jquery.com/jquery.js"></script>
    @yield('javascript')
    <!-- Include all compiled plugins (below), or include individual files as needed -->
    <script src="//netdna.bootstrapcdn.com/bootstrap/3.1.1/js/bootstrap.min.js"></script>

  </body>
  </html>
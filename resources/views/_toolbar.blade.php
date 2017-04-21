<?php
  $authUser = Auth::user();
  $commentsCount = $authUser->newCommentCount();
  $messagesCount = $authUser->newMessageCount();
  $mentions = $authUser->mentions;
  $mentionCount = count($mentions);
  $profileNotificationCount = $commentsCount + $messagesCount;
  $notificationCount = $profileNotificationCount + $mentionCount;
?>
<nav class="navbar navbar-default">
  <div class="container">
    <div class="navbar-header">
      <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
        <span class="sr-only">Toggle navigation</span>
        <span class="glyphicon glyphicon-menu-hamburger"></span> <span>Menu</span> 
        @if ($notificationCount > 0 )
          <span class="badge progress-bar-danger" id="notification-count">{{$notificationCount}}</span>
        @else
          <span class="hidden" id="notification-count">0</span>
        @endif
      </button>
      <a class="navbar-brand" {!! Nexus\Helpers\GoogleAnalyticsHelper::onClickEvent('TopNavigation', 'Home') !!}
      href="/"><span class="glyphicon glyphicon glyphicon-home" aria-hidden="true"></span></a>
    </div>
    <div id="navbar" class="navbar-collapse collapse">

    <ul class="nav navbar-nav">
      <li><a {!! Nexus\Helpers\GoogleAnalyticsHelper::onClickEvent('TopNavigation', 'Users') !!} 
      href="{{ action('Nexus\UserController@index')}}">Users</a></li>
      <li><a {!! Nexus\Helpers\GoogleAnalyticsHelper::onClickEvent('TopNavigation', 'Catch-Up') !!}
      href="{{ action('Nexus\SectionController@leap')}}">Catch-up</a></li> 
      <li><a {!! Nexus\Helpers\GoogleAnalyticsHelper::onClickEvent('TopNavigation', 'Whos Online') !!}
      href="{{ action('Nexus\ActivityController@index')}}">Who's Online</a></li>
      <li><a {!! Nexus\Helpers\GoogleAnalyticsHelper::onClickEvent('TopNavigation', 'Latest') !!}
      href="{{ action('Nexus\SectionController@latest')}}">Latest</a></li>
      <li><a {!! Nexus\Helpers\GoogleAnalyticsHelper::onClickEvent('TopNavigation', 'Search') !!}
      href="{{ action('Nexus\SearchController@index')}}">Search</a></li>
    </ul>

                @if ($mentionCount > 0 )
                <ul class="nav navbar-nav navbar-right">
                  <li class="dropdown">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">
                      <span class="glyphicon glyphicon glyphicon-bell" aria-hidden="true"></span>
                      <span class="badge progress-bar-danger">{{$mentionCount}}</span>
                      <span class="caret"></span>
                    </a>
                    <ul class="dropdown-menu">
                      @foreach ($mentions as $mention)
                      <li><a href="{{Nexus\Helpers\TopicHelper::routeToPost($mention->post)}}"><strong>{{$mention->post->author->username}}</strong> mentioned you in <strong>{{$mention->post->topic->title }}</strong></a></li>
                      @endforeach
                      <li role="separator" class="divider"></li>
                      <li>
                        <form action="{{action('Nexus\MentionController@destroyAll')}}" method="POST">
                          {{ csrf_field() }}
                          {{ method_field('DELETE') }}
                          <li role="presentation">{!! Form::button('<span class="glyphicon glyphicon-ok"></span> Clear All Mentions</button>', ['Type' => 'Submit', 'class' => 'btn btn-link', 'id' => 'Clear All Mentions' ]) !!}</li>
                          {!! Form::close() !!}
                        </li>
                      </ul>
                      @endif
                    </li>
                  </ul>


                  <ul class="nav navbar-nav navbar-right">
                    <li class="dropdown">
                      <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">
                        {{$authUser->username}} &ndash; {{$authUser->popname}} 
                        @if ($profileNotificationCount)
                          <span class="badge progress-bar-danger">{{$profileNotificationCount}}</span>
                        @endif
                        <span class="caret"></span>
                      </a>
                      <ul class="dropdown-menu">
                        <li><a href="{{ action('Nexus\UserController@show', ['user_name' => $authUser->username])}}"> 
                          <span class="glyphicon glyphicon glyphicon-user" aria-hidden="true"></span> Profile 
                          @if ($commentsCount)
                          <span class="badge progress-bar-info">{{$commentsCount}}</span>
                          @endif
                        </a></li>

                        <li><a href="{{action('Nexus\MessageController@index')}}">
                          <span class="glyphicon glyphicon glyphicon-envelope" aria-hidden="true"></span> Inbox 
                          @if ($messagesCount)
                          <span class="badge progress-bar-info">{{$messagesCount}}</span>
                          @endif
                        </a></li>
                        @if ($authUser->sections->count())
                        <li role="separator" class="divider"></li>
                        <li class="dropdown-header">Moderator Goodies</li>
                        <li><a href="{{ action('Nexus\RestoreController@index')}}">
                          <span class="glyphicon glyphicon glyphicon glyphicon-open" aria-hidden="true"></span> Your Archive</a></li>
                          @endif
                          <li role="separator" class="divider"></li>
                          <li><a href="{{ action('Auth\LoginController@logout')}}">
                            <span class="glyphicon glyphicon glyphicon-log-out" aria-hidden="true"></span> Logout</a></li>

                          </ul>
                        </li>
                      </ul>
                    </div><!--/.nav-collapse -->
                  </div>
                </nav>

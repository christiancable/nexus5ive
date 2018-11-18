<?php
$authUser = Auth::user();
$commentsCount = $authUser->newCommentCount();
$messagesCount = $authUser->newMessageCount();
$mentions = $authUser->mentions;
$mentionCount = count($mentions);
$profileNotificationCount = $commentsCount + $messagesCount;
$notificationCount = $profileNotificationCount + $mentionCount;
?>
<div id="top-toolbar" class="border-bottom mb-3">
  <div class="container">
  <nav class="navbar navbar-light bg-light navbar-expand-lg">
      <a class="navbar-brand" {!! App\Helpers\GoogleAnalyticsHelper::onClickEvent('TopNavigation', 'Home') !!} href="/">
        <span class="oi oi-home" title="icon home" aria-hidden="true"></span>
      </a>

      <button type="button" class="navbar-toggler" data-toggle="collapse" data-target="#navbar" aria-expanded="true" aria-controls="navbar">
        <span class="navbar-toggler-icon"></span>
        @if ($notificationCount > 0 )
          <span class="badge badge-danger" id="notification-count">{{$notificationCount}}</span>
        @else
          <span class="sr-only" id="notification-count">0</span>
        @endif
      </button>

      <div id="navbar" class="navbar-collapse collapse" style="">
        <ul class="nav navbar-nav mr-auto">
          <li class="nav-item">
            <a {!! App\Helpers\GoogleAnalyticsHelper::onClickEvent('TopNavigation', 'Users') !!} href="{{ action('Nexus\UserController@index')}}" class="nav-link">Users</a>
          </li>
          <li class="nav-item">
            <a {!! App\Helpers\GoogleAnalyticsHelper::onClickEvent('TopNavigation', 'Catch-Up') !!} href="{{ action('Nexus\SectionController@leap')}}" class="nav-link">Catch-up</a>
          </li> 
          <li class="nav-item">
            <a {!! App\Helpers\GoogleAnalyticsHelper::onClickEvent('TopNavigation', 'Whos Online') !!} href="{{ action('Nexus\ActivityController@index')}}" class="nav-link">Who's Online</a>
          </li>
          <li class="nav-item">
            <a {!! App\Helpers\GoogleAnalyticsHelper::onClickEvent('TopNavigation', 'Latest') !!} href="{{ action('Nexus\SectionController@latest')}}" class="nav-link">Latest</a>
          </li>
          <li class="nav-item">
            <a {!! App\Helpers\GoogleAnalyticsHelper::onClickEvent('TopNavigation', 'Search') !!} href="{{ action('Nexus\SearchController@index')}}" class="nav-link">Search</a>
          </li>
        </ul>

        @if ($mentionCount > 0 )
        <ul class="nav navbar-nav ml-auto">
            <li class="dropdown nav-item"> 
              <a href="#" class="dropdown-toggle nav-link" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false"
              id="mentiondropdown">
                <span class="oi oi-bell" aria-hidden="true"></span>
                <span class="badge  badge-danger">{{$mentionCount}}</span>
              </a>

              <div class="dropdown-menu dropdown-menu-right" aria-labelledby="mentiondropdown">
                  @foreach ($mentions as $mention)
                    <a class="dropdown-item" href="{{App\Helpers\TopicHelper::routeToPost($mention->post)}}">
                    <strong>{{$mention->post->author->username}}</strong> mentioned you in <strong>{{$mention->post->topic->title }}</strong>
                    </a>
                  @endforeach
                  <div role="separator" class="dropdown-divider"></div>
                  
                  <form class="form-inline" action="{{action('Nexus\MentionController@destroyAll')}}" method="POST">
                  {{ csrf_field() }}
                  {{ method_field('DELETE') }}
                    {!! Form::button('<span class="oi oi-check"></span> Clear All Mentions</button>', ['Type' => 'Submit', 'class' => 'btn btn-link', 'id' => 'Clear All Mentions' ]) !!}
                  </form>
              </div>
            </li>
        </ul>
        @endif 

        <ul class="nav navbar-nav">
          <li class="dropdown nav-item">
            
            <a href="#" class="dropdown-toggle nav-link" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"
            id="profiledropdown">
              {{$authUser->username}} &ndash; {{$authUser->popname}} 
              @if ($profileNotificationCount)
                <span class="badge badge-danger">{{$profileNotificationCount}}</span>
              @endif
            </a>

            <div class="dropdown-menu" aria-labelledby="profiledropdown">
                <a class="dropdown-item" href="{{ action('Nexus\UserController@show', ['user_name' => $authUser->username])}}"> 
                  <span class="oi oi-person" aria-hidden="true"></span> Profile 
                  @if ($commentsCount)
                    <span class="badge badge-info">{{$commentsCount}}</span>
                  @endif
                </a>

                <a class="dropdown-item" href="{{action('Nexus\MessageController@index')}}">
                  <span class="oi oi-envelope-closed" aria-hidden="true"></span> Inbox 
                  @if ($messagesCount)
                    <span class="badge badge-info">{{$messagesCount}}</span>
                  @endif
                </a>

                @if ($authUser->sections->count())
                  <div role="separator" class="dropdown-divider"></div>              
                  <div class="dropdown-header dropdown-item">Moderator Goodies</div>
                  <a class="dropdown-item" href="{{ action('Nexus\RestoreController@index')}}">
                    <span class="oi oi-box" aria-hidden="true"></span> Your Archive
                  </a>
                @endif

                <div role="separator" class="dropdown-divider"></div>
                <form class="form-inline" action="{{url('/logout')}}" method="POST">
                  {{ csrf_field() }}
                  <button class="btn btn-link"><span class="oi oi-account-logout" aria-hidden="true"></span> Logout </button>
                </form>
            </div>
          </li>
        </ul>
      </div>
  </nav>
  </div>
</div>
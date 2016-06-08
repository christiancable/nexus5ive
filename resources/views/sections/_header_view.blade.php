<div class="container">
    <h1>{{$section->title}}</h1>
    <span class="lead">{!! Nexus\Helpers\NxCodeHelper::nxDecode($section->intro) !!}</span>
    <p>Moderated by: <a href="{{ action('Nexus\UserController@show', ['username' => $section->moderator->username])}}">{{$section->moderator->username}}</a></p>
</div>
    
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
    


 


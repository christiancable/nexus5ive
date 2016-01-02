<li>
    <a href="{{action('Nexus\MessageController@show', ['selected' => $activity->user->id])}}">
    <span class="glyphicon glyphicon glyphicon-envelope" aria-hidden="true"></span>
    </a>
     -
    <strong><a href="{{action('Nexus\UserController@show', ['user_name' => $activity->user->username])}}">
    {{$activity->user->username}} ({{$activity->user->popname}})
    </a></strong> -
    <a href="{{$activity->route}}">{!! $activity->text !!}</a>
     - <small class="text-muted"> {{$activity->time->diffForHumans()}}  </small>
</li>
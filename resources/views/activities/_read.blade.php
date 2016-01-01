<li>
    <strong><a href="{{action('Nexus\UserController@show', ['user_name' => $activity->user->username])}}">
    {{$activity->user->username}}
    </a></strong> -
    <a href="{{$activity->route}}">{{$activity->text}}</a>
     - <small class="text-muted"> {{$activity->time->diffForHumans()}}  </small>
</li>
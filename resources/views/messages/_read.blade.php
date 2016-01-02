<li>
    <a href="{{action('Nexus\UserController@show', ['user_name' => $message->author->username])}}">
        <strong>{{$message->author->username}}</strong>
    </a> - <small class="text-muted"> {{$message->time->diffForHumans()}} </small><br>
        {{$message->text}}
</li>
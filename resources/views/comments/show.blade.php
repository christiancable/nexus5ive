<li><strong><a href="{{ action('Nexus\UserController@show', ['user_name' => $comment->author->username]) }}">{{$comment->author->username}}</a></strong> - 
@if ($comment->read === true)
{{$comment->text}}
@else 
<strong>{{$comment->text}}</strong>
@endif 
</li>
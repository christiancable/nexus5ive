@if (!str_is($post->message_title, ""))
<div class="alert alert-info" role="alert">
    <p>{{$post->author->user_name}} switched subject to <strong>{{$post->message_title}}</strong></p>
</div>
@endif

<div class="panel panel-default" id="{{$post->message_id}}">
    <div class="panel-heading">
        <p><a href="{{ action('Nexus\UserController@show', ['user_name' => $post->author->user_name]) }}">{{$post->author->user_name}}</a> ({{$post->message_popname}})
        <span class="pull-right">{{ date('D, F jS Y - H:i', strtotime($post->message_time)) }}</span></p>
    </div>        

    <div class="panel-body">
    <p>{{ $post->message_text }}</p>
       
    </div>
    {{-- <li>{{$post->update_user_id}}</li> --}}
</div>
    {{-- <li>{{$post->message_html}}</li> --}}

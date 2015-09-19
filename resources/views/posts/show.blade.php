<div class="panel panel-primary" id="{{$post->message_id}}">

    @if (!str_is($post->message_title, ""))
    <div class="panel-heading">
        <h3 class="panel-title">{{$post->message_title}}</h3>
    </div>
    @endif

    <div class="panel-body">

        @if ($readProgress < $post->message_time)
            <span class="pull-right text-info">{{ date('D, F jS Y - H:i', strtotime($post->message_time)) }}</span>
        @else 
            <span class="pull-right text-muted">{{ date('D, F jS Y - H:i', strtotime($post->message_time)) }}</span>   
        @endif 

        <span><a href="{{ action('Nexus\UserController@show', ['username' => $post->author->username]) }}">{{$post->author->username}}</a> ({{$post->message_popname}})</span>
        <hr>

        <p>{{ $post->message_text }}</p>

    </div>
    {{-- <li>{{$post->update_user_id}}</li> --}}
</div>
    {{-- <li>{{$post->message_html}}</li> --}}
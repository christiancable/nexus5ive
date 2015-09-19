<div class="panel panel-primary" id="{{$post->message_id}}">

    @if (!str_is($post->message_title, ""))
    <div class="panel-heading">
        <h3 class="panel-title">{{$post->message_title}}</h3>
    </div>
    @endif

    <div class="panel-body">

        @if ($readProgress < $post->message_time)
            <span class="pull-right text-info">{{ $post->message_time->diffForHumans() }}</span>
        @else 
            <span class="pull-right text-muted">{{ $post->message_time->diffForHumans() }}</span>   
        @endif 

        <span><strong>Anonymous</strong> (Hidden User)</span>
        <hr>

        <p>{{ $post->message_text }}</p>

    </div>
    {{-- <li>{{$post->update_user_id}}</li> --}}
</div>
    {{-- <li>{{$post->message_html}}</li> --}}
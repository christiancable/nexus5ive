@if (!str_is($post->message_title, ""))
<div class="alert alert-info" role="alert">
    <p>Someone switched subject to <strong>{{$post->message_title}}</strong></p>
</div>
@endif

<div class="panel panel-default" id="{{$post->message_id}}">
    <div class="panel-heading">
        <p><strong>Anonymous</strong> (Hidden User)
        <span class="pull-right text-muted">{{$post->message_time->diffForHumans()}}</span></p>
    </div>        

    <div class="panel-body">
    <p>{{ $post->message_text }}</p>
       
    </div>
    {{-- <li>{{$post->update_user_id}}</li> --}}
</div>
    {{-- <li>{{$post->message_html}}</li> --}}

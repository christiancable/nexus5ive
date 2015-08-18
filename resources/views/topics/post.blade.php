@if (!str_is($post->message_title, ""))
<div class="alert alert-info" role="alert">
    <p>{{$post->author->user_name}} switch subject to <strong>{{$post->message_title}}</strong></p>
</div>
@endif



<div class="panel panel-default">
    <div class="panel-heading">
        <p><a href="{{ url("/users/{$post->author->user_name}") }}">{{$post->author->user_name}}</a> ({{$post->message_popname}})
        <span class="pull-right">{{ date('D, F jS Y - H:i', strtotime($post->message_time)) }}</span></p>
    
    
    </div>        

    <div class="panel-body">
    <p>{!! nl2br($post->message_text) !!}</p>
       
    </div>
    {{-- <li>{{$post->update_user_id}}</li> --}}
</div>
    {{-- <li>{{$post->message_html}}</li> --}}

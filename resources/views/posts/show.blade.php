<div class="panel panel-primary" id="{{$post->id}}">

    @if (!str_is($post->title, ""))
    <div class="panel-heading">
        <h3 class="panel-title">{{$post->title}}</h3>
    </div>
    @endif

    <div class="panel-body">

        @if ($readProgress < $post->time)
            <span class="pull-right text-info">{{ date('D, F jS Y - H:i', strtotime($post->time)) }}</span>
        @else 
            <span class="pull-right text-muted">{{ date('D, F jS Y - H:i', strtotime($post->time)) }}</span>   
        @endif 

        @if (isset($post->author))
            <span><a href="{{ action('Nexus\UserController@show', ['username' => $post->author->username]) }}">{{$post->author->username}}</a> ({{$post->popname}})</span>
        @else
            <span>Unknown User (Unknown User)</span>
        @endif
        <hr>
        <p>{!! Nexus\Helpers\nxCodeHelper::nxDecode($post->text) !!}</p>

    </div>
    {{-- <li>{{$post->update_user_id}}</li> --}}
</div>
    {{-- <li>{{$post->html}}</li> --}}
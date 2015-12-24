<div class="panel panel-primary" id="{{$post->id}}">

    @if (!str_is($post->title, ""))
    <div class="panel-heading">
        <h3 class="panel-title">{{$post->title}}</h3>
    </div>
    @endif

    <div class="panel-body">

        @if ($readProgress < $post->time)
            <span class="pull-right text-info">{{ $post->time->diffForHumans() }}</span>
        @else 
            <span class="pull-right text-muted">{{ $post->time->diffForHumans() }}</span>   
        @endif 

        <span><strong>Anonymous</strong> (Hidden User)</span>
        <hr>

        <p>{{ $post->text }}</p>

    </div>
    {{-- <li>{{$post->update_user_id}}</li> --}}
</div>
    {{-- <li>{{$post->html}}</li> --}}
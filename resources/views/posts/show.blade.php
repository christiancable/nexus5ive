<div class="panel panel-primary" id="{{$post->id}}">

    @if (!str_is($post->title, ""))
    <div class="panel-heading">
        <h3 class="panel-title">{{$post->title}}</h3>
    </div>
    @endif

    <div class="panel-body">
        <div class="row">
            <div class="col-sm-12 col-md-6">       
            @if (isset($post->author))
                <span><a href="{{ action('Nexus\UserController@show', ['username' => $post->author->username]) }}">{{$post->author->username}}</a> ({{$post->popname}})</span>
            @else
                <span>Unknown User (Unknown User)</span>
            @endif
            </div>
            <div class="col-sm-12 col-md-6">
            <?php
                $postTime = $post->time;
                $formattedTime = date('D, F jS Y - H:i', strtotime($post->time));
            ?>  
            @if ($readProgress < $postTime)
                <span class="pull-right text-info visible-lg visible-md">{{ $formattedTime }}</span>
                <span class="visible-sm visible-xs text-info">{{ $formattedTime }}</span>
            @else 
                <span class="pull-right text-muted visible-lg visible-md">{{ $formattedTime }}</span>   
                <span class="visible-sm visible-xs text-muted">{{ $formattedTime }}</span>   
            @endif     
          </div>
        </div>
        <hr>
        <p>{!! Nexus\Helpers\NxCodeHelper::nxDecode($post->text) !!}</p>
    </div>
</div>
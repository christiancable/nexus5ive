<div class="panel panel-primary" id="{{$post->id}}">
    @if (!str_is($post->title, ""))
    <div class="panel-heading">
        <h3 class="panel-title">{{$post->title}}</h3>
    </div>
    @endif

    <div class="panel-body break-long-words">
        <div class="row">

            {{-- display author --}}
            <div class="col-sm-12 col-md-6">       
            @if($post->topic->secret && $userCanSeeSecrets == false)
                 <span><strong>Anonymous</strong> (Hidden User)</span>
            @else 
                @if (isset($post->author))
                {!! $post->author->present()->profileLink !!} &ndash; {{$post->popname}}
                @else
                    <span><strong>Unknown User &ndash; Unknown User</span>
                @endif
            @endif 
            </div>

            {{-- post time --}}
            <div class="col-sm-12 col-md-6">
                <?php
                $postTime = $post->time;
                // if we are anonymous them we want to see fuzzy times
                if ($post->topic->secret && $userCanSeeSecrets == false) {
                    $formattedTime = $post->time->diffForHumans();
                } else {
                    $formattedTime = date('D, F jS Y - H:i', strtotime($post->time));
                }
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

        @if ($post->editor)
              {{-- if we are anonymous them we want to see fuzzy times  --}}
              @if($post->topic->secret && $userCanSeeSecrets == false)
                  <small class="pull-right text-muted">Edited by <strong>Anonymous</strong> around {{$post->updated_at->diffForHumans()}}</small>
              @else 
                <small class="pull-right text-muted">Edited by <strong>{{$post->editor->username}}</strong> at {{$post->updated_at->format('D, F jS Y - H:i')}}</small>
              @endif
        @endif 
    </div>
</div>
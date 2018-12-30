
<div class="panel panel-primary">
    <div class="panel-heading">
        <h2 class="panel-title"><a href="{{ action('Nexus\SectionController@show', ['id' => $subSection->id])}}">{{$subSection->title}}</a></h2>
    </div>

    <div class="panel-body">
        <div class="row">
            <div class="col-sm-9">
                {!! App\Helpers\NxCodeHelper::nxDecode($subSection->intro)  !!}
            </div>
            <div class="col-sm-3">
                @if($subSection->topicCount || $subSection->sectionCount)

                    <p class="small text-muted">
                        @if($subSection->topicCount)
                            {{$subSection->topicCount}} 
                            @if($subSection->topicCount > 1)
                                topics 
                            @else
                                topic
                            @endif
                        @endif            

                        @if($subSection->topicCount && $subSection->sectionCount)
                            and
                        @endif

                        @if($subSection->sectionCount)
                            {{$subSection->sectionCount}} 
                            @if($subSection->sectionCount > 1)
                                sections 
                            @else
                                section
                            @endif
                        @endif
                @endif

                @if($subSection->most_recent_post)
                    <br/>
                    Latest Post in <a href="{{ action('Nexus\TopicController@show', ['id' => $subSection->most_recent_post->topic->id])}}">{{$subSection->most_recent_post->topic->title}}</a>, {{$subSection->most_recent_post->time->diffForHumans()}}</p> 
                @endif
                </p>
            </div>      
        </div>
    </div>
</div>

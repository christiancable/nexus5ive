<div class="card mb-3">
    <div class="card-header bg-primary text-white">
        <h2 class="h4 card-title mb-0"><a class="text-white d-block"
                href="{{ action('Nexus\SectionController@show', ['section' => $subSection->id]) }}">{{ $subSection->title }}</a>
        </h2>
    </div>
    <div class="card-body">
        {!! App\Helpers\NxCodeHelper::nxDecode($subSection->intro) !!}
    </div>
    @php
        $topicCount = $subSection->topicCount;
        $subSectionCount = $subSection->sectionCount;
    @endphp
    @if($topicCount || $subSectionCount)
        <footer class="card-footer">
            <p class="small text-muted mb-0">

                @if($topicCount)
                    {{ $topicCount }}

                    @if($topicCount > 1)
                        topics
                    @else
                        topic
                    @endif

                @endif

                @if($subSection->topicCount && $subSectionCount)
                    and
                @endif

                @if($subSectionCount)

                    {{ $subSectionCount }}

                    @if($subSectionCount > 1)
                        sections
                    @else
                        section
                    @endif
                @endif


                @if($subSection->most_recent_post)
                    <br>Latest Post in <a href="{{ 
		action('Nexus\TopicController@show', ['topic' => $subSection->most_recent_post->topic->id])}}">
                        {{-- @todo egear load the most_recent_post->topic --}}
                        {{ $subSection->most_recent_post->topic->title }}</a>,
                    {{ $subSection->most_recent_post->time->diffForHumans() }}
            </p>
    @endif
    </footer>
    @endif
</div>
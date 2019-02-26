<div class="card mb-3">
  <div class="card-header bg-primary text-white">
    <h2 class="h4 card-title mb-0"><a class="text-white d-block" href="{{ action('Nexus\SectionController@show', ['id' => $subSection->id])}}">{{$subSection->title}}</a></h2>
  </div>
  <div class="card-body">
    {!! App\Helpers\NxCodeHelper::nxDecode($subSection->intro)  !!}
  </div>
@if($subSection->topicCount || $subSection->sectionCount)
  <footer class="card-footer">
	<p class="small text-muted mb-0">

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


@if($subSection->most_recent_post)
	<br>Latest Post in <a href="{{ 
		action('Nexus\TopicController@show', ['id' => $subSection->most_recent_post->topic->id])}}">
		{{-- @todo egear load the most_recent_post->topic --}}
		{{$subSection->most_recent_post->topic->title}}</a>, {{$subSection->most_recent_post->time->diffForHumans()}}</p> 
@endif
</footer>
@endif
</div>

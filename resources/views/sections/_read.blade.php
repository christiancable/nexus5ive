
    <div class="panel panel-primary">
        <div class="panel-heading">
            <h2 class="panel-title"><a href="{{ action('Nexus\SectionController@show', ['id' => $subSection->id])}}">{{$subSection->title}}</a></h2>
        </div>

        <div class="panel-body">
            <p>{!! Nexus\Helpers\NxCodeHelper::nxDecode($subSection->intro)  !!}</p>
             @if($subSection->topics->count() || $subSection->sections->count())
            <p class="small text-muted">Contains:
	            @if($subSection->topics->count())
		            {{$subSection->topics->count()}} 
	            	@if($subSection->topics->count() > 1)
		            	topics 
	            	@else
		            	topic
	            	@endif
	            @endif            
	            @if($subSection->topics->count() && $subSection->sections->count())
	            and
	            @endif
	            @if($subSection->sections->count())
	            	{{$subSection->sections->count()}} 
	            	@if($subSection->sections->count() > 1)
		            	sections 
	            	@else
		            	section
	            	@endif
	            @endif
            @endif
            </p>
			{{-- <p class="small text-muted" >Latest Post in [TOPIC TITLE](link to topic), time</p> --}}
        </div>
    </div>

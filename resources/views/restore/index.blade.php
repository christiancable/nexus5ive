@extends('layouts.master')

@section('meta')
<title>Restore</title>
@endsection

@section('breadcrumbs')
@include('_breadcrumbs', $breadcrumbs)
@endsection 

@section('content')

<div class="container">
    <h1>Your Archive</h1>

    <span class="lead">
    "But you know what? It's never too late to get it back."
    </span>
</div>
<hr>

@if ($destinationSections->count() == 0)
	<div class="container">
		<div  class="alert alert-warning">
		You cannot restore any sections or topics because you do not moderate any place to restore them to. Sorry!
		</div>
	</div>
@else 
	
<div id="accordions">
	<div class="container">
		<h2>Sections</h2>
		<hr>
	</div>
	
	@if ($trashedSections->count() != 0)
    	<div class="panel-group container" id="trashedSectionsAccordion" 
    	    role="tablist" aria-multiselectable="true">
            <div class="panel panel-default">
                
                <div class="panel-heading" role="tab" id="trashedSections">
                  <h2 class="panel-title">
                    <a class="collapsed" role="button" data-toggle="collapse" 
                        data-parent="#trashedSectionsAccordion" 
                        href="#trashedSectionsPanel" 
                        aria-expanded="false" 
                        aria-controls="trashedSectionsPanel">
                        <i class="indicator glyphicon glyphicon-chevron-right"></i> Sections to Restore
                    </a>
                  </h2>
                </div>
                
                <div id="trashedSectionsPanel" class="panel-collapse collapse" role="tabpanel" 
                  aria-labelledby="trashedSections">
                    <div class="panel-body">
                    @foreach($trashedSections as $section)
                        @include('restore.section', $section)
                    @endforeach
                  </div>
                </div>
              </div>
            </div>
	@else 
	
    	<div class="container">
    		<div  class="alert alert-info">
    			You don't have any archived sections to restore.
    		</div>
    	</div>
	@endif 
	

    <div class="container">
		<h2>Topics</h2>
		<hr>
	</div>
	
	@if ($trashedTopics->count() != 0)
    	<div class="panel-group container" id="trashedTopicsAccordion" 
    	    role="tablist" aria-multiselectable="true">
            <div class="panel panel-default">
                
                <div class="panel-heading" role="tab" id="trashedTopics">
                  <h2 class="panel-title">
                    <a class="collapsed" role="button" data-toggle="collapse" 
                        data-parent="#trashedTopicsAccordion" 
                        href="#trashedTopicsPanel" 
                        aria-expanded="false" 
                        aria-controls="trashedTopicsPanel">
                        <i class="indicator glyphicon glyphicon-chevron-right"></i> Topics to Restore
                    </a>
                  </h2>
                </div>
                
                <div id="trashedTopicsPanel" class="panel-collapse collapse" role="tabpanel" 
                  aria-labelledby="trashedTopics">
                    <div class="panel-body">
                    @foreach($trashedTopics as $topic)
                        @include('restore.topic', $topic)
                    @endforeach
                  </div>
                </div>
              </div>
            </div>
	@else 
	
    	<div class="container">
    		<div  class="alert alert-info">
    			You don't have any archived topics to restore.
    		</div>
    	</div>
	@endif 
@endif          

</div>

@endsection

@section('javascript')
<script type="text/javascript">

function toggleChevron(e) {
    $(e.target)
        .prev('.panel-heading')
        .find("i.indicator")
        .toggleClass('glyphicon-chevron-down glyphicon-chevron-right');
}

$('#accordions').on('hidden.bs.collapse', toggleChevron);
$('#accordions').on('shown.bs.collapse', toggleChevron);
</script>
@endsection
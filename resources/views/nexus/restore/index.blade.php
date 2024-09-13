@extends('layouts.master')

@section('meta')
<title>Restore</title>
@endsection

@section('breadcrumbs')
@include('nexus._breadcrumbs', $breadcrumbs)
@endsection 

@section('content')

<div class="container">

@include('shared._heading', [
            $heading = 'Your Archive',
            $lead = '"_But you know what? It\'s never too late to get it back._"',
            $introduction = "These are your archived sections and topics. They only visible to you until you restore them."
])


@if ($destinationSections->count() == 0)
  <div  class="alert alert-warning">
  You cannot restore any sections or topics because you do not moderate any place to restore them to. Sorry!
  </div>
@else 
	
<h2>Archived Sections</h2>

  <button class="disclose btn btn-success col-12" type="button" data-toggle="collapse" data-target="#sections" aria-expanded="false" aria-controls="sections">
    <span class="oi oi-chevron-right mr-2"></span>View Sections to Restore
  </button>


<div class="collapse" id="sections">
	@if ($trashedSections->count() != 0)
    @foreach($trashedSections as $section)
        @include('restore.section', $section)
    @endforeach    
	@else 
  
    <div  class="alert alert-info my-3">
      You don't have any sections to restore.
    </div>
  
	@endif 
</div>

<hr>

<h2>Archived Topics</h2>

  <button class="disclose btn btn-success col-12" type="button" data-toggle="collapse" data-target="#topics" aria-expanded="false" aria-controls="topics">
    <span class="oi oi-chevron-right mr-2"></span>View Topics to Restore
  </button>


<div class="collapse" id="topics">
	@if ($trashedTopics->count() != 0)

      @foreach($trashedTopics as $topic)
          @include('restore.topic', $topic)
      @endforeach    

	@else 
  
    <div  class="alert alert-info my-3">
      You don't have any topics to restore.
    </div>
  
	@endif 
</div>

@endif
</div> 

@endsection

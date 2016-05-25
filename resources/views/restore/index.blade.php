@extends('layouts.master')

@section('meta')
<title>Restore</title>
@endsection

@section('breadcrumbs')
@include('_breadcrumbs', $breadcrumbs)
@endsection 

@section('content')

<div class="container">
    <h1>Restore</h1>

    <span class="lead">
    But you know what? It's never too late to get it back.
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
	<div class="container">
		<h2>Archived Sections</h2>
		<hr>
	</div>
	@foreach ($trashedSections as $section)
	    @include('restore.section', $section)
	@endforeach

@endif          
@endsection

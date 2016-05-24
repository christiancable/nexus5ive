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
</div>
<hr>

@foreach ($trashedSections as $section)
    @include('restore.section', $section)
@endforeach

@endsection

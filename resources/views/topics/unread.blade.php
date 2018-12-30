@extends('layouts.master')

@section('meta')
<title>Latest Posts</title>
@endsection

@section('breadcrumbs')
@include('_breadcrumbs', $breadcrumbs)
@endsection 

@section('content')
<div class="container">
    @include('shared._heading', [$heading, $lead])
    @if (count($topics))
        @foreach ($topics as $topic)
            @include('topics._latest', $topic)
        @endforeach
    @endif
</div>
@endsection
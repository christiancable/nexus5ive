@extends('nexus.layouts.master')

@section('meta')
<title>Latest Posts</title>
@endsection

@section('breadcrumbs')
@include('nexus._breadcrumbs', $breadcrumbs)
@endsection 

@section('content')
<div class="container">
    @include('nexus.shared._heading', [$heading, $lead])
    @if (count($topics))
        @foreach ($topics as $topic)
            @include('nexus.topics._latest', $topic)
        @endforeach
    @endif
</div>
@endsection
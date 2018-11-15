@extends('layouts.master')

@section('meta')
<title>Latest Posts</title>
@endsection

@section('breadcrumbs')
@include('_breadcrumbs', $breadcrumbs)
@endsection 

@section('content')
<div class="container">
    <h1 class="display-4">{{$heading}}</h1>
    <p class="lead">{{$lead}}</p>
    <hr>
    @if (count($topics))
        @foreach ($topics as $topic)
            @include('topics._latest', $topic)
        @endforeach
    @endif
</div>
@endsection
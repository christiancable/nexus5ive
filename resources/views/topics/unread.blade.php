@extends('layouts.master')

@section('meta')
<title>Latest Posts</title>
@endsection

@section('breadcrumbs')
@include('_breadcrumbs', $breadcrumbs)
@endsection 

@section('content')
<div class="container">

    <div class="content">
        <h1 class="display-4">{{$heading}}</h1>
        <p class="lead">{{$lead}}</p>
    </div>

    <hr>

    <div class="content">
        @if (count($topics))
            @foreach ($topics as $topic)
            @include('topics._latest', $topic)
            @endforeach
        @endif
    </div>

</div>
@endsection
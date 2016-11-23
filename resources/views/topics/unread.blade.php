@extends('layouts.master')

@section('meta')
<title>Unread Posts</title>
@endsection

@section('breadcrumbs')
@include('_breadcrumbs', $breadcrumbs)
@endsection 

@section('content')
<div class="container">

    <div class="content">
        {{-- <h1>Continue the Conversation</h1>
        <p class="lead">Here's what you've missed&hellip;</p> --}}
        <h1>{{$heading}}</h1>
        <p class="lead">{{$lead}}</p>
        
    </div>

    <hr>

    <div class="content">

        @if (count($topics))

        
        @foreach ($topics as $topic)
         @include('topics._read-compact', $topic)
        @endforeach
        
        @endif
    </div>

</div>
@endsection
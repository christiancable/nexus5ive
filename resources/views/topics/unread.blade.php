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
        <div class="well">
            <h2>
                @if ($topic->unreadPosts(Auth::user()->id))
                <span class="glyphicon glyphicon-fire text-danger" aria-hidden="true"></span>
                @else
                <span class="glyphicon glyphicon-comment" aria-hidden="true"></span>
                @endif

                <a href="{{ action('Nexus\TopicController@show', ['topic_id' => $topic->id])}}"> {{$topic->title}}</a>
            </h2>
            <p>{!!nl2br($topic->intro)!!}</p>
            @if ($topic->most_recent_post_time)
            <p class="small text-muted">Latest Post {{$topic->most_recent_post_time->diffForHumans()}}</p>
            @endif
        </div>
        @endforeach
        @endif
    </div>

</div>
@endsection
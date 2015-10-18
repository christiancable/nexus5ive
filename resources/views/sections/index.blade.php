@extends('layouts.master')

@section('meta')
<title>{{$section->section_title}}</title>
@endsection

@section('content')
<div class="container">

    <div class="content">
        <h1>{{$section->section_title}}</h1>
        <p class="lead">{{$section->section_intro}}</p>
        <p>Moderated by: <a href="{{ action('Nexus\UserController@show', ['username' => $section->moderator->username])}}">{{$section->moderator->username}}</a></p>
    </div>

    @if (session('alert'))
    <div class="content">
        <div class="alert alert-warning" role="alert">No updated topics found. Why not start a new conversation or read more sections?</div>
    </div>
    @endif 

    @if (session('topic'))
    <div class="content">
        <div class="alert alert-success" role="alert">People have been talking! New posts found in <strong><a href="{{ action('Nexus\TopicController@show', ['topic_id' => session('topic')->topic_id])}}"> {{session('topic')->topic_title}}</a></strong></div>
    </div>
    @endif 

    <hr>

    <div class="content">


        @if (count($section->topics))
        @foreach ($section->topics as $topic)
        <div class="well">
            <h2>
                @if ($topic->unreadPosts(Auth::user()->id))
                <span class="glyphicon glyphicon-fire text-danger" aria-hidden="true"></span>
                @else
                <span class="glyphicon glyphicon-comment" aria-hidden="true"></span>
                @endif

                <a href="{{ action('Nexus\TopicController@show', ['topic_id' => $topic->topic_id])}}"> {{$topic->topic_title}}</a>
            </h2>
            <p>{!!nl2br($topic->topic_description)!!}</p>
            @if ($mostRecentPostTime = $topic->most_recent_post_time)
                <p class="small text-muted">Latest Post {{$mostRecentPostTime->diffForHumans()}}</p>
            @endif
        </div>
        @endforeach
        @endif

        @if (count($section->sections))
        <hr>
        <div class="row">
            @foreach ($section->sections as $subSection)
            <div class="col-md-4">
                <div class="panel panel-primary">
                    <div class="panel-heading">
                        <h2 class="panel-title"><a href="{{ action('Nexus\SectionController@show', ['section_id' => $subSection->section_id])}}">{{$subSection->section_title}}</a></h2>
                    </div>

                    <div class="panel-body">
                        <p><em>{{$subSection->section_intro}}</em></p>
                        {{--  <p><a class="btn btn-default" href="{{ action('Nexus\SectionController@show', ['section_id' => $subSection->section_id])}}" role="button">View details &raquo;</a></p> --}}
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        @endif
    </div>

</div>
@endsection
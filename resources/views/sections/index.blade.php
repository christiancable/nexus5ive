@extends('layouts.master')

@section('meta')
<title>{{$section->section_title}}</title>
@endsection

@section('content')
<div class="page-header">
    <div class="container">
        <h1>{{$section->section_title}}</h1>
        <p>{{$section->section_intro}}</p>
        <p>Moderated by: <a href="{{ action('Nexus\UserController@show', ['username' => $section->moderator->username])}}">{{$section->moderator->username}}</a></p>
    </div>
</div>


{{--                 @if($section->parent)
<p>Return to <a href="{{ url("/{$section->parent->section_id}") }}">{{$section->parent->section_title}}</a><p>
@endif  --}}

<div class="container">
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
            <p class="small text-muted">Latest Post {{$topic->most_recent_post_time->diffForHumans()}}</p>
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
                            <p><a class="btn btn-default" href="{{ action('Nexus\SectionController@show', ['section_id' => $subSection->section_id])}}" role="button">View details &raquo;</a></p>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
    
    </div>
</div>
@endsection
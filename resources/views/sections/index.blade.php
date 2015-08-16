@extends('layouts.master')

@section('meta')
<title>{{$section->section_title}}</title>
@endsection

@section('content')
        <div class="container">
            <div class="content">

                @if($section->parent)
                    <p>Return to <a href="{{ url("/{$section->parent->section_id}") }}">{{$section->parent->section_title}}</a><p>
                @endif 

                <h1 class="title">{{$section->section_title}}</h1>
                <p>Moderated by: {{$section->moderator->user_name}}</p>

                 @if (count($section->topics))
                    <h2>Topics</h2>
                    <ul>
                    @foreach ($section->topics as $topic)
                        <li>
                        <h3><a href="{{ url("/{$section->section_id}/$topic->topic_id") }}">{{$topic->topic_title}}</a></h3>
                        <p>{{$topic->topic_description}}</p>
                        </li>
                    @endforeach
                    </ul>
                @endif

                @if (count($section->sections))
                    <h2>Sub Sections</h2>
                    <ul>
                    @foreach ($section->sections as $subSection)
                        <li>
                        <h3><a href="{{ url("/{$subSection->section_id}") }}">{{$subSection->section_title}}</a></h3>
                        <p>{{$subSection->section_intro}}</p>
                        </li>
                    @endforeach
                    </ul>
                @endif


            </div>
        </div>
@endsection
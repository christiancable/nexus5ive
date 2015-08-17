@extends('layouts.master')

@section('meta')
<title>{{$section->section_title}}</title>
@endsection

@section('content')

<div class="jumbotron">
      <div class="container">
        <h1>{{$section->section_title}}</h1>
        <p>{{$section->section_intro}}</p>
        <p>Moderated by: {{$section->moderator->user_name}}</p>
        {{-- <p><a class="btn btn-primary btn-lg" href="#" role="button">Learn more &raquo;</a></p> --}}
      </div>
</div>


{{--                 @if($section->parent)
                    <p>Return to <a href="{{ url("/{$section->parent->section_id}") }}">{{$section->parent->section_title}}</a><p>
                @endif  --}}


        <div class="container">
            <div class="content">

                 @if (count($section->topics))
                    <h2>Topics</h2>
                    <ul>
                    @foreach ($section->topics as $topic)
                        <div class="well">
                        <h3><a href="{{ url("/{$section->section_id}/$topic->topic_id") }}">{{$topic->topic_title}}</a></h3>
                        <p>{{$topic->topic_description}}</p>
                        </div>
                    @endforeach
                    </ul>
                @endif

          
                @if (count($section->sections))
                <hr>
                      <div class="row">
                    @foreach ($section->sections as $subSection)
                        <div class="col-md-4">
                        <h3><a href="{{ url("/{$subSection->section_id}") }}">{{$subSection->section_title}}</a></h3>
                        <p><em>{{$subSection->section_intro}}</em></p>
                         <p><a class="btn btn-default" href="{{ url("/{$subSection->section_id}") }}" role="button">View details &raquo;</a></p>
                        </div>
                    @endforeach
                    </div>
                @endif


            </div>
        </div>
@endsection
@extends('layouts.master')

@section('meta')
<title>{{$topic->topic_title}}</title>
@endsection

@section('content')
        <div class="container">
            <div class="content">
               
                <p>Return to <a href="{{ url("/{$topic->section_id}") }}">{{$topic->section->section_title}}</a><p>
            

                <h1 class="title">{{$topic->topic_title}}</h1>

                @forelse($topic->posts as $post)
                    @include('topics.post', $post)
                @empty
                    <p>No Posts.</p>
                @endforelse

            </div>
        </div> 
@endsection

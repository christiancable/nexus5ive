@extends('layouts.master')

@section('meta')
<title>{{$topic->topic_title}}</title>
@endsection

@section('content')


<div class="page-header">
    <div class="container">
        <h1>{{$topic->topic_title}}</h1>
        
    </div>
</div>


<div class="container">
    <div class="content">


                <p>Return to <a href="{{ action('Nexus\SectionController@show', ['section_id' => $topic->section_id]) }}">{{$topic->section->section_title}}</a><p>
                
                <?php
                $postsChunk = $posts->paginate(10);
                $reverseArray = [];
                foreach ($postsChunk as $post) {
                    $reverseArray[] = $post;
                }
                $reverseArray = array_reverse($reverseArray);
                ?>

                @forelse($reverseArray as $post)
                    @include('topics.post', $post)
                @empty
                    <p class="alert alert-warning">No Posts.</p>
                @endforelse

                {{-- check to see if we should show this --}}
                @include('posts.create', $topic)

                {!! $postsChunk->render() !!}
      </div>
</div>
@endsection

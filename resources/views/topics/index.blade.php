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


{{--                 @if($section->parent)
<p>Return to <a href="{{ url("/{$section->parent->section_id}") }}">{{$section->parent->section_title}}</a><p>
@endif  --}}

<div class="container">
    <div class="content">

      {{--   <div class="container">
            <div class="content"> --}}
               
                <p>Return to <a href="{{ url("/{$topic->section_id}") }}">{{$topic->section->section_title}}</a><p>
                
                <?php $postsChunk = $posts->paginate(10) ?>
                @forelse($postsChunk as $post)
                    @include('topics.post', $post)
                @empty
                    <p class="alert alert-warning">No Posts.</p>
                @endforelse


                {!! $postsChunk->render() !!}
     {{--        </div>
        </div>  --}}
      </div>
      </div>
@endsection

@extends('layouts.master')

@section('meta')
<title>{{$topic->title}}</title>
@endsection

@section('breadcrumbs')
@include('_breadcrumbs', $breadcrumbs)
@endsection 

@section('content')


<div class="container">
        <h1>{{$topic->title}}</h1>
        @if ($topic->intro) 
        <p class="lead">{!! nl2br($topic->intro)  !!}</p>
        @endif
</div>
<hr>

<div class="container">
    <div class="content">
                <?php
                $postsChunk = $posts->paginate(10);
                $reverseArray = [];
                foreach ($postsChunk as $post) {
                    $reverseArray[] = $post;
                }
                $reverseArray = array_reverse($reverseArray);
                ?>

                @if($topic->secret) 


                    @if($userCanSeeSecrets) 
                       <div class="alert alert-danger" role="alert">
                            <p><strong>This topic is anonymous</strong>. You can see who wrote each post because you are privileged. <strong>Please respect people's anonymity</strong>.</p>
                        </div>
                    @else
                        <div class="alert alert-danger" role="alert">
                            <p><strong>This topic is anonymous</strong>. However, the Moderator <strong>{{$topic->section->moderator->username}}</strong> and the BBS administrator are able to see who wrote each post.</p>
                        </div>
                    @endif 

                @endif

                @forelse($reverseArray as $post)
                    @if($topic->secret && $userCanSeeSecrets == false) 
                        @include('posts.showhidden', compact('post', 'readProgress'))
                    @else
                        @include('posts.show', compact('post', 'readProgress'))
                    @endif 
                @empty
                    <p class="alert alert-warning">No Posts.</p>
                @endforelse

                {{-- check to see if we should show this --}}
                @if($readonly === true) 
                    <div class="alert alert-danger" role="alert">
                        <p><strong>This topic is closed</strong>. You cannot add a new post.</p>
                    </div>
                @else 
		      {{-- only show the post box if we are on the first page --}}
		      @if ($postsChunk->currentPage() === 1)
                      	  @include('posts.create', $topic)
		      @endif

                @endif 

                {!! $postsChunk->render() !!}
      </div>
</div>
@endsection

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
        <p class="lead">{!! App\Helpers\NxCodeHelper::nxDecode($topic->intro)  !!}</p>
    @endif
    @include('topics._subscribe', compact('topic','unsubscribed'))
</div>
<hr>

<div class="container">
    <div class="content">
    <?php
        $latestPost = $posts->get()->first();
        $postsChunk = $posts->paginate(config('nexus.pagination'));
        Auth::user()->removeMentions($postsChunk->items());
        $postsArray = [];
        foreach ($postsChunk as $post) {
            $postsArray[] = $post;
        }
        if (!Auth::user()->viewLatestPostFirst) {
            $postsArray = array_reverse($postsArray);
        }
    ?>

    {{-- show post box --}}
    @if (Auth::user()->viewLatestPostFirst) 
        @include('topics._addpost', compact('postsChunk', 'readOnly'))
    @endif

    {{-- render posts --}}
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

    @forelse($postsArray as $post) 

        @if($topic->section->moderator->id === Auth::user()->id)
            @include('posts.moderate', compact('post', 'readProgress'))
        @else 
        {{-- if we are on the last post and we  are the author and it is recent
        the display the moderate view so a user can edit their post --}}
	    @if (($post['id'] == $latestPost['id']) && ($post->author->id == Auth::user()->id) && ($post->time->diffInSeconds() <= config('nexus.recent_edit') )) 
                <?php $hideDelete = true ?>
                @include('posts.moderate', compact('post', 'readProgress', 'noDelete'))
	    @else
	        @include('posts.show', compact('post', 'readProgress', 'userCanSeeSecrets'))
	    @endif
        @endif 

        @empty
            <p class="alert alert-warning">No Posts.</p>
    @endforelse

    {{-- show post box --}}
    @if (!Auth::user()->viewLatestPostFirst) 
        @include('topics._addpost', compact('postsChunk', 'readOnly'))
    @endif
    {!! $postsChunk->render() !!}
    </div>
</div>

@endsection
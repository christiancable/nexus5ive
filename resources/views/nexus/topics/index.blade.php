@extends('nexus.layouts.master')

@section('meta')
    <title>{{ $topic->title }}</title>
@endsection

@section('breadcrumbs')
    @include('nexus._breadcrumbs', $breadcrumbs)
@endsection

@section('content')
    <div class="container">

        <x-heading :heading="$topic->title" :lead="$topic->intro" />

        <section class="d-flex flex-row-reverse justify-content-between">
            @include('nexus.topics._subscribe', compact('topic', 'unsubscribed'))
            @if ($topic->section->moderator->id === Auth::user()->id)
                @include('nexus.shared._editToggle')
            @endif
        </section>
    </div>

    <div class="container">
        <div class="content">
            <?php
            $latestPost = $posts->get()->first();
            $postsChunk = $posts->simplePaginate(config('nexus.pagination'));
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
                @include('nexus.topics._addpost', compact('postsChunk', 'readonly', 'replyingTo'))
            @endif

            {{-- render posts --}}
            @if ($topic->secret)
                @if ($userCanSeeSecrets)
                    <div class="alert alert-danger" role="alert">
                        <p><strong>This topic is anonymous</strong>. You can see who wrote each post because you are
                            privileged. <strong>Please respect people's anonymity</strong>.</p>
                    </div>
                @else
                    <div class="alert alert-danger" role="alert">
                        <p><strong>This topic is anonymous</strong>. However, the Moderator
                            <strong>{{ $topic->section->moderator->username }}</strong> and the BBS administrator are able
                            to see who wrote each post.</p>
                    </div>
                @endif
            @endif

            @forelse($postsArray as $post)
                <?php $allowDelete = true; ?>
                @if ($topic->section->moderator->id === Auth::user()->id)
                    @include('nexus.post._moderate', compact('post', 'readProgress'))
                @else
                    {{-- if we are on the last post and we are the author and it is recent then display the moderate view so a user can edit their post --}}
                    @if (
                        $post['id'] == $latestPost['id'] &&
                            $post->author->id == Auth::user()->id &&
                            $post->time->diffInSeconds() <= config('nexus.recent_edit'))
                        <?php
                        $forceCogMenu = true; //show cog menu for recent post
                        $allowDelete = false;
                        ?>
                        @include('nexus.post._moderate', compact('post', 'readProgress', 'allowDelete'))
                    @else
                        @include('nexus.post._view', compact('post', 'readProgress', 'userCanSeeSecrets'))
                    @endif
                @endif

            @empty
                <p class="alert alert-warning">No Posts.</p>
            @endforelse

            {{-- show post box --}}
            @if (!Auth::user()->viewLatestPostFirst)
                @include('nexus.topics._addpost', compact('postsChunk', 'readonly', 'replyingTo'))
            @endif
            {!! $postsChunk->render() !!}
        </div>
    </div>

@endsection

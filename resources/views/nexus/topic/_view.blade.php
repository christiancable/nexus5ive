<?php
$status = \App\Helpers\ViewHelper::getTopicStatus(Auth::user(), $topic);
$topicLink = action('App\Http\Controllers\Nexus\TopicController@show', ['topic' => $topic->id]);
?>

<div class="card mb-3">
    <div class="card-body position-relative">
        @if($topic->sticky)
            <x-heroicon-s-bookmark class="icon_large text-warning position-absolute top-0 end-0 mt-2 me-2" title="Sticky" />
        @endif

        <x-topic-heading title="{{ $topic->title }}" :link=$topicLink :status=$status />

        <p class="card-text">{!! App\Helpers\NxCodeHelper::nxDecode($topic->intro) !!}</p>

        @if ($topic->most_recent_post)
            <footer class="d-flex flex-row-reverse">
                <p class="small text-muted">Latest Post
                    @if ($topic->most_recent_post->title)
                        <em>&ldquo;{{ $topic->most_recent_post->title }}&rdquo;</em>
                    @endif
                    by
                    @if ($topic->secret)
                    <strong>Anonymous</strong>
                    @else
                    <x-profile-link :user="$topic->most_recent_post->author" /> 
                    @endif
                    {{ $topic->most_recent_post->time->diffForHumans() }}
                </p>
            </footer>
        @endif

    </div>
</div>

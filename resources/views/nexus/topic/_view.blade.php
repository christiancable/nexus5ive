<?php
$status = \App\Helpers\ViewHelper::getTopicStatus(Auth::user(), $topic);
$topicLink = action('App\Http\Controllers\Nexus\TopicController@show', ['topic' => $topic->id]);

if ($topic->most_recent_post) {
    $authorLink = $topic->most_recent_post->author->present()->profileLink;
    if ($topic->secret == true) {
        $authorLink = '<strong>Anonymous</strong>';
    }
}
?>

<div class="card mb-3">
    <div class="card-body">


        <x-topic-heading title="{{ $topic->title }}" :link=$topicLink :status=$status />

        <p class="card-text">{!! App\Helpers\NxCodeHelper::nxDecode($topic->intro) !!}</p>

        @if ($topic->most_recent_post)
            <footer class="d-flex flex-row-reverse">
                <p class="small text-muted">Latest Post
                    @if ($topic->most_recent_post->title)
                        <em>&ldquo;{{ $topic->most_recent_post->title }}&rdquo;</em>
                    @endif
                    by
                    {!! $authorLink !!} {{ $topic->most_recent_post->time->diffForHumans() }}
                </p>
            </footer>
        @endif

    </div>
</div>

<?php
$latestPost = $topic->latestPostByTime;
$view = $views[$topic->id] ?? null;
$status = \App\Helpers\ViewHelper::getTopicStatus(Auth::user(), $topic, $view);

$pattern = '/\[spoiler-\](.*)\[-spoiler\]/iU';

$replyLink = action('App\Http\Controllers\Nexus\TopicController@show', [
    'topic' => $topic->id,
    'reply' => true
]);

$sectionLink = action('App\Http\Controllers\Nexus\SectionController@show', ['section' => $topic->section->id]);
$topicLink = action('App\Http\Controllers\Nexus\TopicController@show', ['topic' => $topic->id]);
?>


<div class="card mb-3">
        <div class="card-header position-relative">
        @if($topic->sticky)
            <x-heroicon-s-bookmark class="icon_large text-warning position-absolute top-0 end-0 mt-2 me-2" title="Sticky" />
        @endif
        <x-topic-heading title="{{ $topic->title }}" :link=$topicLink :status=$status />

        @if($latestPost)
        <small class="card-subtitle mb-2 text-muted">
            Latest post {{ $latestPost->time?->diffForHumans() ?? 'Date unknown' }} by
            @if ($topic->secret)
                <strong>Anonymous</strong>
            @else
                <x-profile-link :url="action('App\Http\Controllers\Nexus\UserController@show', ['user' => $latestPost->author->username])" :username="$latestPost->author->username" />
            @endif
            in
            <a href="{{$sectionLink}}">{{$topic->section->title}}</a>
        </small>
        @endif
        </div>
        @if(!$status['unsubscribed'] && $latestPost)
            <div class="card-body">
                    <p class="card-text text-muted">
                        <em>{!! substr(strip_tags(App\Helpers\NxCodeHelper::nxDecode(preg_replace($pattern, 'XXXXXX', $latestPost->text))), 0, 140) !!}</em>&hellip;
                    </p>
                    @can('create', [App\Models\Post::class, $topic])
                    <footer class="d-flex justify-content-end">
                        <a href="{{$replyLink}}" class="btn btn-primary"> Reply </a>
                    </footer>
                    @endcan
            </div>
        @endif
</div>

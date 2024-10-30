<?php
$status = \App\Helpers\ViewHelper::getTopicStatus(Auth::user(), $topic);

// author display - hide author for anon topics
$authorLink = $topic->most_recent_post->author->present()->profileLink;
if ($topic->secret == true) {
    $authorLink = '<strong>Anonymous</strong>';
}
        
// remove the spoilers
$pattern = '/\[spoiler-\](.*)\[-spoiler\]/iU';
$unspoiled = preg_replace($pattern, 'XXXXXX', $topic->most_recent_post->text);

$replyLink = action('App\Http\Controllers\Nexus\TopicController@show', [
    'topic' => $topic->id,
    'reply' => true
]);

// links
$sectionLink = action('App\Http\Controllers\Nexus\SectionController@show', ['section' => $topic->section->id]);
$topicLink = action('App\Http\Controllers\Nexus\TopicController@show', ['topic' => $topic->id]);
?>


<div class="card mb-3">
        <div class="card-header">
        <x-topic-heading title="{{ $topic->title }}" :link=$topicLink :status=$status />
        
        <small class="card-subtitle mb-2 text-muted">
            Latest post {{$topic->most_recent_post->time->diffForHumans()}} by
            {!! $authorLink !!} in 
            <a href="{{$sectionLink}}">{{$topic->section->title}}</a>
        </small>
        </div>
        @if($status['unsubscribed'] != true)
            <div class="card-body">
                    <p class="card-text text-muted">
                        <em>{!! substr(strip_tags(App\Helpers\NxCodeHelper::nxDecode($unspoiled)), 0, 140) !!}</em>&hellip;
                    </p>
                    <footer class="d-flex justify-content-end">
                    <a href="{{$replyLink}}" class="btn btn-primary"> Reply </a>
                    </footer>
            </div>
        @endif 
</div>


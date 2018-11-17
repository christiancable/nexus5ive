<?php
$status = \App\Helpers\ViewHelper::getTopicStatus(Auth::user(), $topic);

// display style

$textClass = 'text-primary';
$icon = 'oi oi-comment-square';

if ($status['unsubscribed']) {
    $textClass = 'text-muted';
    $icon = 'oi oi-thumb-down'; 
} elseif ($status['new_posts']) { 
    $textClass = 'text-danger';
    $icon = 'oi oi-fire';
} elseif ($status['never_read']) {
    $textClass = 'text-success';
    $icon = 'oi oi oi-star';
}

// author display - hide author for anon topics
$authorLink = $topic->most_recent_post->author->present()->profileLink;
if ($topic->secret == true) {
    $authorLink = '<strong>Anonymous</strong>';
}
        
// remove the spoilers
$pattern = '/\[spoiler-\](.*)\[-spoiler\]/iU';
$unspoiled = preg_replace($pattern, 'XXXXXX', $topic->most_recent_post->text);

$replyLink = action('Nexus\TopicController@show', [
    'topic_id' => $topic->id,
    'reply' => true
]);

// links
$sectionLink = action('Nexus\SectionController@show', ['section_id' => $topic->section->id]);
$topicLink = action('Nexus\TopicController@show', ['topic_id' => $topic->id]);
?>


<div class="card mb-3">
        <div class="card-header">
        <a href="{{$topicLink}}">
            <h2 class="card-title"><span class="{{$textClass}} {{$icon}}"></span> {{$topic->title}}</h2>
        </a>
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


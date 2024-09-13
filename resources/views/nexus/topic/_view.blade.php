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


if ($topic->most_recent_post) {
    $authorLink = $topic->most_recent_post->author->present()->profileLink;
    if ($topic->secret == true) {
        $authorLink = '<strong>Anonymous</strong>';
    }
}

$topicLink = action('App\Http\Controllers\Nexus\TopicController@show', ['topic' => $topic->id]);

?>

<div class="card mb-3">
  <div class="card-body">

    <a href="{{$topicLink}}">
        <h2 class="card-title"><span class="{{$textClass}} {{$icon}}"></span> {{$topic->title}}</h2>
    </a>
    <p class="card-text">{!! App\Helpers\NxCodeHelper::nxDecode($topic->intro) !!}</p>

    @if ($topic->most_recent_post)
        <footer class="d-flex flex-row-reverse">
            <p class="small text-muted">Latest Post 
            @if($topic->most_recent_post->title)
                <em>&ldquo;{{$topic->most_recent_post->title}}&rdquo;</em> 
            @endif 
            by
            {!!$authorLink!!} {{$topic->most_recent_post->time->diffForHumans()}}</p>
        </footer>
    @endif
        
  </div>
</div>
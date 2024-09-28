<?php
$status = \App\Helpers\ViewHelper::getTopicStatus(Auth::user(), $topic);

// display style

$textClass = 'text-primary';
$icon = 'default';

if ($status['unsubscribed']) {
    $icon = 'unsubscribed';
    $textClass = 'text-muted';
} elseif ($status['new_posts']) { 
    $icon = 'new_posts';
    $textClass = 'text-danger';
} elseif ($status['never_read']) {
    $icon = 'never_read';
    $textClass = 'text-success';
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
        <h2 class="card-title">
        
        @switch($icon)
            @case('unsubscribed')
                <x-heroicon-s-hand-thumb-down class="icon_topic {{$textClass}} " />
                @break
            
            @case('new_posts')
                <x-heroicon-s-fire class="icon_topic {{$textClass}}" />
                @break

            @case('never_read')
                <x-heroicon-s-star class="icon_topic {{$textClass}}" />
                @break

            @default
                <x-heroicon-s-chat-bubble-bottom-center-text class="icon_topic {{$textClass}}" /> 
        @endswitch
        {{$topic->title}}    
        </h2>
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
<?php
$status = \App\Helpers\ViewHelper::getTopicStatus(Auth::user(), $topic);

if ($status['unsubscribed']) {
    $textClass = 'text-muted';
    $panelClass = '';
    $icon = 'glyphicon-eye-close';
} elseif ($status['new_posts']) {
    $textClass = 'text-danger';
    $panelClass = 'panel-danger';
    $icon = 'glyphicon-fire';
} elseif ($status['never_read']) {
    $textClass = 'text-warning';
    $panelClass = 'panel-warning';
    $icon = 'glyphicon-asterisk';
} else {
    $textClass = 'text-primary';
    $panelClass = 'panel-default';
    $icon = 'glyphicon-comment';
}

// remove the spoilers
$pattern = '/\[spoiler-\](.*)\[-spoiler\]/iU';
$unspoiled = preg_replace($pattern, 'XXXXXX', $topic->most_recent_post->text);
?>

<div class="panel {{$panelClass}}">
  <!-- Default panel contents -->
  <a href="{{ action('Nexus\TopicController@show', ['topic_id' => $topic->id])}}" class="{{$textClass}}">
      <div class="panel-heading">
        <h2 class="{{$textClass}}">
            <span class="glyphicon {{$icon}} {{$textClass}}" aria-hidden="true"></span>
            {{$topic->title}}
        </h2>
    </div>
</a>

<!-- List group -->
<ul class="list-group">
    <li class="list-group-item">Latest post {{$topic->most_recent_post->time->diffForHumans()}} by 
        @if($topic->secret == true)
        <strong>Anonymous</strong>
        @else 
        {!! $topic->most_recent_post->author->present()->profileLink !!}
        @endif 
        in 
        <a href="{{ action('Nexus\SectionController@show', ['section_id' => $topic->section->id])}}">{{$topic->section->title}}</a>
    </li>
</ul>
@if($status['unsubscribed'] != true)
<div class="panel-body">
    <p><a href="{{ action('Nexus\TopicController@show', ['topic_id' => $topic->id])}}" class="text-muted">
    <em>{!! substr(strip_tags(App\Helpers\NxCodeHelper::nxDecode($unspoiled)), 0, 140) !!}</em>
    &hellip;</a></p>
    <a class="btn btn-primary pull-right" href="{{ 
            action('Nexus\TopicController@show', 
            [
                'topic_id' => $topic->id,
                'reply' => true
            ])
        }}" role="button">
        <span class="glyphicon glyphicon-share-alt glyphicon-flip-horizontal" aria-hidden="true"></span>  Reply</a>
</div>
@endif

</div>

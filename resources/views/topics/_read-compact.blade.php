<?php
$status = \Nexus\Helpers\ViewHelper::getTopicStatus(Auth::user(), $topic);

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
    $textClass = '';
    $panelClass = 'panel-default';
    $icon = 'glyphicon-comment';
}
?>

<div class="panel {{$panelClass}}">
  <!-- Default panel contents -->
        <a href="{{ action('Nexus\TopicController@show', ['topic_id' => $topic->id])}}" class="{{$textClass}}">
  <div class="panel-heading">
        <span class="glyphicon {{$icon}}" aria-hidden="true"></span>
        <strong>{{$topic->title}}</strong>
  </div>
        </a>
  
  <!-- List group -->
  <ul class="list-group">
    <li class="list-group-item">Latest post {{$topic->most_recent_post->time->diffForHumans()}} by 
        @if($topic->secret == true)
        <strong>Anonymous</strong>
        @else 
        <a href="{{ action('Nexus\UserController@show', ['username' => $topic->most_recent_post->author->username]) }}"><strong>{{$topic->most_recent_post->author->username}}</strong></a>
        @endif 

         in 
      <a href="{{ action('Nexus\SectionController@show', ['section_id' => $topic->section->id])}}">{{$topic->section->title}}</a>

        </li>

    </ul>
    <div class="panel-body">
        <p><a href="{{ action('Nexus\TopicController@show', ['topic_id' => $topic->id])}}">{!! substr(strip_tags(Nexus\Helpers\NxCodeHelper::nxDecode($topic->most_recent_post->text)), 0, 140) !!}&hellip;</a></p>
    </div>

</div>

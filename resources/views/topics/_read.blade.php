<?php
$status = \Nexus\Helpers\ViewHelper::getTopicStatus(Auth::user(), $topic);
?>
<div class="well">
<h2>
	@if ($status['unsubscribed'])
		<?php $textClass = 'text-muted' ?>
		<a href="{{ action('Nexus\TopicController@show', ['topic_id' => $topic->id])}}" class="{{$textClass}}"> 
		<span class="glyphicon glyphicon-eye-close {{$textClass}}" aria-hidden="true"></span>

    @elseif ($status['new_posts'])
	    <?php $textClass = 'text-danger' ?>
	    <a href="{{ action('Nexus\TopicController@show', ['topic_id' => $topic->id])}}" class="{{$textClass}}"> 
	    <span class="glyphicon glyphicon-fire {{$textClass}}" aria-hidden="true"></span>

    @elseif ($status['never_read'])
	    <?php $textClass = 'text-warning' ?>
	    <a href="{{ action('Nexus\TopicController@show', ['topic_id' => $topic->id])}}" class="{{$textClass}}"> 
	    <span class="glyphicon glyphicon-asterisk {{$textClass}}" aria-hidden="true"></span>
	    
    @else 
    	<?php $textClass = '' ?>
    	<a href="{{ action('Nexus\TopicController@show', ['topic_id' => $topic->id])}}" class="{{$textClass}}"> 
	    <span class="glyphicon glyphicon-comment" aria-hidden="true"></span>
    @endif  
    {{$topic->title}}</a>
</h2>
<p class="break-long-words">{!! Nexus\Helpers\NxCodeHelper::nxDecode($topic->intro) !!}</p>
@if ($topic->most_recent_post)
    <p class="small text-muted">Latest Post by 
    @if($topic->secret == true)
          Anonymous,
    @else 
        <a href="{{ action('Nexus\UserController@show', ['username' => $topic->most_recent_post->author->username]) }}">{{$topic->most_recent_post->author->username}}</a>, 
    @endif 
        {{$topic->most_recent_post->time->diffForHumans()}}</p>
@endif
</div>
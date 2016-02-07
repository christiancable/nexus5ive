<div class="well">
<h2>
    @if ($topic->unreadPosts(Auth::user()->id))
    <span class="glyphicon glyphicon-fire text-danger" aria-hidden="true"></span>
    @else
    <span class="glyphicon glyphicon-comment" aria-hidden="true"></span>
    @endif

    <a href="{{ action('Nexus\TopicController@show', ['topic_id' => $topic->id])}}"> {{$topic->title}}</a>
</h2>
<p>{!!nl2br($topic->intro)!!}</p>
@if ($mostRecentPostTime = $topic->most_recent_post_time)
    <p class="small text-muted">Latest Post {{$mostRecentPostTime->diffForHumans()}}</p>
@endif
</div>
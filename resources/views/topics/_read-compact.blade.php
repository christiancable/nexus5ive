<div class="panel panel-default">
  <!-- Default panel contents -->
  <div class="panel-heading">
      <strong><a href="{{ action('Nexus\TopicController@show', ['topic_id' => $topic->id])}}">{{$topic->title}}</a></strong>
  </div>
  
  <!-- List group -->
  <ul class="list-group">
    <li class="list-group-item">Last post {{$topic->most_recent_post->time->diffForHumans()}} by 
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

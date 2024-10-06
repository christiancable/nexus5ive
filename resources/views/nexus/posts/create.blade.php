@if($topic->readonly) 
  <div class="alert alert-warning" role="alert">
      <p>{!!__('nexus.topic.closed.moderator')!!}</p>
  </div>
@endif 

@livewire('post-compose', ['topic' => $topic, 'reply' => $replyingTo])




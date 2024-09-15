@if($topic->readonly) 
  <div class="alert alert-warning" role="alert">
      <p><strong>This topic is closed</strong> but you are allowed to post because you can moderate this section.</p>
  </div>
@endif 

@livewire('post-compose', ['topic' => $topic, 'reply' => $replyingTo])




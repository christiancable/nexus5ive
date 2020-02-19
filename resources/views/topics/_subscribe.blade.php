
	{!! Form::open(array(
		'route' => ['topic.updateSubscription', 'topic' => $topic->id],
        'class' => 'form' ))
    !!}
	    <button class="btn btn-link">
			@if($unsubscribed)
				<span class="oi oi-check text-success" aria-hidden="true"></span><span> Subscribe to this topic</span>
				{!! Form::hidden('command', 'subscribe') !!}
			@else
				<span class="oi oi-x text-danger" aria-hidden="true"></span><span> Unsubscribe from this topic</span>
				{!! Form::hidden('command', 'unsubscribe') !!}
			@endif
	    </button>
	{!! Form::close() !!}

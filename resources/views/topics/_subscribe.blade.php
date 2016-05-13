<section class="pull-right">
	{!! Form::open(array(
		'route' => ['topic.updateSubscription', 'id' => $topic->id],
        'class' => 'form' ))
    !!}
	    <button class="btn btn-link">
			@if($unsubscribed)
				<span class="glyphicon glyphicon-eye-open" aria-hidden="true"></span><span> Resubscribe to this topic</span>
				{!! Form::hidden('command', 'subscribe') !!}
			@else
				<span class="glyphicon glyphicon-eye-close" aria-hidden="true"></span><span> Unsubscribe from this topic</span>
				{!! Form::hidden('command', 'unsubscribe') !!}
			@endif
	    </button>
	{!! Form::close() !!}
</section>
@if($topic->readonly) 
<div class="alert alert-warning" role="alert">
    <p><strong>This topic is closed</strong> but you are allowed to post because you can moderate this section.</p>
</div>
@else 
@endif

{!! Form::open(['url' => 'posts']) !!}


{!! Form::hidden('topic_id', $topic->id) !!}

    <div class="form-group">
        {!! Form::label('title', 'Subject') !!}
        {!! Form::text('title', null, ['class'=> 'form-control']) !!}
    </div>

    <div class="form-group">
{{--         {!! Form::label('message_text', 'Subject') !!} --}}
        {!! Form::textarea('text', null, ['class'=> 'form-control']) !!}
    </div>

     <div class="form-group">
        {!! Form::submit('Add Comment', ['class'=> 'btn btn-primary form-control']) !!}
    </div>
{!! Form::close() !!}

{{-- the only error we have is if the user tries to leave a blank comment --}}
@if ($errors->any())
    <p class="alert alert-danger">
        Only a monster would try to leave an empty comment! 
    </p>
@endif 




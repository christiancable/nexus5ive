{!! Form::open(['url' => 'comments']) !!}
{!! Form::hidden('user_id', $user->id) !!}
{!! Form::hidden('redirect_user', $user->username) !!}
    <div class="form-group">
        {!! Form::text('text', null, ['class'=> 'form-control', 'autofocus', 'placeholder' => 'Leave a comment']) !!}
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
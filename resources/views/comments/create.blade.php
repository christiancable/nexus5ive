{!! Form::open(['url' => 'comments']) !!}
{!! Form::hidden('user_id', $user->user_id) !!}
{!! Form::hidden('redirect_user', $user->user_name) !!}
    <div class="form-group">
        {!! Form::label('text', 'Leave Comment') !!}
        {!! Form::text('text', null, ['class'=> 'form-control']) !!}
    </div>

     <div class="form-group">
        {!! Form::submit('Add Comment', ['class'=> 'btn btn-primary form-control']) !!}
    </div>
{!! Form::close() !!}
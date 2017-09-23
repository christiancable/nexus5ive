{!! 
Form::open([
    'action' => 'Nexus\CommentController@destroyAll',
    'method' => 'delete'
])
!!}
{!! Form::hidden('user_id', $user->id) !!}
{!! Form::hidden('redirect_user', $user->username) !!}
     <div class="form-group">
        {!! Form::submit('Clear All Comments', ['class'=> 'btn btn-danger form-control']) !!}
    </div>
{!! Form::close() !!}

<div class="well">
{!! Form::model($post, array(
            'route' => ['posts.update', $post->id],
            'class' => 'form',
            'method' => 'PATCH'
            )) !!}
{!! Form::hidden('id', $post->id) !!}

    <div class="form-group">
        {!! Form::text('title', null, ['class'=> 'form-control', 'placeholder'=>'Subject']) !!}
    </div>

    <div class="form-group">
        {!! Form::textarea('text', null, ['class'=> 'form-control', 'id'=>'postText']) !!}
    </div>

<div class="row">    
    <div class="col-md-12">
        <div class="form-group">          
            {!! Form::button("<span class='glyphicon glyphicon-pencil'></span>&nbsp;&nbsp;Update" , array('type' => 'submit', 'class' => "btn pull-right btn-info")) !!}


        </div>
    </div>
</div>
{!! Form::close() !!}

{{-- the only error we have is if the user tries to leave a blank comment --}}
 @if (Session::get('postForm') == $post->id)
    @if ($errors->any())
        <p class="alert alert-danger">
            Comments cannot be empty. Please delete the comment instead. 
        </p>
    @endif 
@endif
</div>

<div class="well">
<?php $formName = $post->id ?>
{!! Form::open(
    array(
        'route'     => ['posts.update', $post->id],
        'class'     => 'form',
        'method'    => 'PATCH',
        'name'      => $formName,
        )
) !!}
{!! Form::hidden('id', $post->id) !!}

    <div class="form-group">
        {!! Form::text("form[$formName][title]", $post->title, ['class'=> 'form-control', 'placeholder'=>'Subject']) !!}
    </div>

    <div class="form-group">
        {!! Form::textarea("form[$formName][text]", $post->text, ['class'=> 'form-control', 'id'=>'postText']) !!}
    </div>

<div class="row">    
    <div class="col-md-12">
        <div class="form-group">          
            {!! Form::button("<span class='glyphicon glyphicon-pencil'></span>&nbsp;&nbsp;Update",
                array(
                    'type'  => 'submit',
                    'class' => "btn pull-right btn-info", 
                    'value' => $formName
                    )
            ) !!}
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

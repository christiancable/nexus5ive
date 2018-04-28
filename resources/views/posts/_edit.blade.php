<?php 
    $formName = $post->id;
    $errorBag = 'postUpdate' . $post->id;
 ?>
<div class="well">
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
        {!! Form::textarea("form[$formName][text]", $post->text, ['class'=> 'form-control']) !!}
    </div>

<div class="row">    
    <div class="col-sm-12">
        <div class="form-group">          
            {!! Form::button("<span class='glyphicon glyphicon-pencil'></span>&nbsp;&nbsp;Save Changes",
                array(
                    'type'  => 'submit',
                    'class' => "btn pull-right btn-info col-xs-12 col-sm-3", 
                    'value' => $formName
                    )
            ) !!}
        </div>
    </div>
</div>
{!! Form::close() !!}
 
@if ($errors->$errorBag->any())
<br/>
<div class="alert alert-danger" role="alert">
    <ul>
    @foreach($errors->$errorBag->all() as $error)
    <li>{{ $error }}</li>
    @endforeach
    </ul>
</div>
@endif 

</div>

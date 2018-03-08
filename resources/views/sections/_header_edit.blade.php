<?php
    $formName = 'section'.$section->id;
?>
{!! Form::open(
    array(
        'route'     => ['section.update', $section->id],
        'class'     => 'form',
        'method'    => 'PATCH',
        'name'      => $formName,
        )
    ) 
!!}

{!! Form::hidden("form[$formName][id]", $section->id) !!}
{!! Form::hidden("form[$formName][parent_id]", $section->parent_id) !!}
{!! Form::hidden("form[$formName][user_id]", $section->user_id) !!}
{!! Form::hidden("form[$formName][weight]", $section->weight) !!}

<div class="form-group">
    {!! Form::text("form[$formName][title]", $section->title, ['class'=> 'form-control', 'placeholder'=>'Title']) !!}
</div>

<div class="form-group">
    {!! Form::textarea("form[$formName][intro]", $section->intro, ['class'=> 'form-control']) !!}
</div>
<?php
    $submitLabel = 'Save Changes';
    $submitIcon = 'glyphicon-pencil';
    $submitType = 'btn-info';
?>


<div class="row">    
    <div class="col-sm-12">
        <div class="form-group">          
        {!! 
            Form::button("<span class='glyphicon glyphicon-pencil'></span>&nbsp;&nbsp;Save Changes",
                array(
                    'type'  => 'submit',
                    'class' => "btn pull-right btn-info col-xs-12 col-sm-3", 
                    'value' => $formName
                    )
                ) 
        !!}
        </div>
    </div>
</div>

{!! Form::close() !!}

@if (Session::get('form') == $formName)
@if ($errors->any())
    <br/>
    <div class="row">
    <div class="col-sm-12">
        <p class="alert alert-danger">
       
        You need to <strong>give your section a title</strong>. Otherwise; <em>chaos</em>.
        </p>
        </div>
    </div>
@endif 
@endif



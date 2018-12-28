<?php
    $formName = 'section'.$section->id;
    $errorBag = 'sectionUpdate' . $section->id;
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


<div class="d-flex justify-content-end">    
    
        <div class="form-group">          
            {!! Form::button("<span class='oi oi-pencil mr-2'></span>Save Changes",
                    array(
                        'type'  => 'submit',
                        'class' => "btn btn-success"
                        )
            ) !!}
        </div>
    
</div>
{!! Form::close() !!}

@if ($errors->$errorBag->any())
    @include('forms._errors', ['errors' => $errors->$errorBag->all()])
@endif 
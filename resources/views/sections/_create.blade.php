{!! 
    Form::open(array(
    'route' => ['section.store'],
    'class' => 'form'
)) !!}
{!! Form::hidden("parent_id", $section->id) !!}

<div class="form-group">    
    {!! Form::label("title",'Title', ['class' => 'sr-only']) !!}
    {!! Form::text("title", null, ['class'=> 'form-control', 'placeholder' => 'Title'])!!}
</div>
<div class="form-group">
    {!! Form::label("intro",'Introduction', ['class' => 'sr-only']) !!}
    {!! Form::textarea("intro", null, ['class'=> 'form-control', 'rows' => '3', 'placeholder' => 'Introduction'])!!}
</div>

<div class="d-flex flex-row-reverse bd-highlight">    
    <div class="form-group ml-2">          
        {!! Form::button("<span class='oi oi-plus mr-2'></span>Add Section",
            array(
                'type'  => 'submit',
                'class' => "btn btn-success"
                )
        ) !!}
    </div>
</div>
   
{!! Form::close() !!}

@if ($errors->sectionCreate->all())
 @include('forms._createErrors', ['errors' => $errors->sectionCreate->all(), 'formContainer' => 'newSectionPanel'])
@endif 

<div class="well">
    {!! 
        Form::open(array(
        'route' => ['section.store'],
        'class' => 'form'
    )) !!}
    <?php
        $formName = 'sectionCreate';
        $submitLabel = 'Add Section';
        $submitIcon = 'glyphicon-plus-sign';
        $submitType = 'btn-primary';
    ?>

    {!! Form::hidden("form[$formName][parent_id]", $section->id) !!}
    {!! Form::hidden("form[$formName][user_id]", Auth::user()->id) !!}  

    <div class="form-group">    
        {!! Form::label("form[$formName][title]",'Title', ['class' => 'hidden']) !!}
        {!! Form::text("form[$formName][title]", null, ['class'=> 'form-control', 'placeholder' => 'Title'])!!}
    </div>
    <div class="form-group">
        {!! Form::label("form[$formName][intro]",'Introduction', ['class' => 'hidden']) !!}
        {!! Form::textarea("form[$formName][intro]", null, ['class'=> 'form-control', 'rows' => '3', 'placeholder' => 'Introduction'])!!}
    </div>
    
    <div class="row form-inline">
        <div class="col-md-3 col-xs-12 pull-right">
                {!! Form::button("<span class='glyphicon $submitIcon'></span>&nbsp;&nbsp;" . $submitLabel, array('type' => 'submit', 'class' => "btn pull-right  col-xs-12 $submitType")) !!}
        </div>
</div>

   
{!! Form::close() !!}
</div>
 @include('forms._createErrors', ['errors' => $errors, 'formName' => $formName, 'formContainer' => 'newSectionPanel'])
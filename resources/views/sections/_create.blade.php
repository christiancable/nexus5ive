<div class="well">
    {!! 
        Form::open(array(
        'route' => ['section.store'],
        'class' => 'form'
    )) !!}
    <?php
        $submitLabel = 'Add Section';
        $submitIcon = 'glyphicon-plus-sign';
        $submitType = 'btn-primary';
    ?>

    {!! Form::hidden("parent_id", $section->id) !!}

    <div class="form-group">    
        {!! Form::label("title",'Title', ['class' => 'hidden']) !!}
        {!! Form::text("title", null, ['class'=> 'form-control', 'placeholder' => 'Title'])!!}
    </div>
    <div class="form-group">
        {!! Form::label("intro",'Introduction', ['class' => 'hidden']) !!}
        {!! Form::textarea("intro", null, ['class'=> 'form-control', 'rows' => '3', 'placeholder' => 'Introduction'])!!}
    </div>
    
    <div class="row form-inline">
        <div class="col-md-3 col-xs-12 pull-right">
                {!! Form::button("<span class='glyphicon $submitIcon'></span>&nbsp;&nbsp;" . $submitLabel, array('type' => 'submit', 'class' => "btn pull-right  col-xs-12 $submitType")) !!}
        </div>
</div>

   
{!! Form::close() !!}
</div>

@if ($errors->sectionCreate->all())
 @include('forms._createErrors', ['errors' => $errors->sectionCreate->all(), 'formContainer' => 'newSectionPanel'])
@endif 

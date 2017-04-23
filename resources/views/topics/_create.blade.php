<div class="well">
    {!! 
        Form::open(array(
            'route' => ['topic.store'],
            'class' => 'form'
            )) !!}
        <?php
        $formName = 'topicCreate';
        $submitLabel = 'Add Topic';
        $submitIcon = 'glyphicon-plus-sign';
        $submitType = 'btn-primary';
        ?>
        {!! Form::hidden("form[$formName][section_id]", $section->id) !!}
        {!! Form::hidden("form[$formName][secret]", false) !!}  
        {!! Form::hidden("form[$formName][readonly]", false) !!}

        <div class="form-group">    
            {!! Form::label("form[$formName][title]",'Title', ['class' => 'hidden']) !!}
            {!! Form::text("form[$formName][title]", null, ['class'=> 'form-control', 'placeholder' => 'Title'])!!}
        </div>
        <div class="form-group">
            {!! Form::label("form[$formName][intro]",'Introduction', ['class' => 'hidden']) !!}
            {!! Form::textarea("form[$formName][intro]", null, ['class'=> 'form-control', 'rows' => '3', 'placeholder' => 'Introduction'])!!}
        </div>

        <div class="row form-inline">

            <div class="col-md-2">
                <div class="checkbox">
                    <label>{!! Form::checkbox("form[$formName][secret]")!!} Anonymous</label>
                </div>
                <div class="checkbox">
                    <label>{!! Form::checkbox("form[$formName][readonly]")!!} Read Only</label>
                </div>
            </div>

            <div class="col-md-7 form-group">     
                <label>Order {!! Form::selectRange("form[$formName][weight]", 0, 10, null, ['class' => 'form-control'])!!} </label>
            </div>

            <div class="col-md-3">
                {!! Form::button("<span class='glyphicon $submitIcon'></span>&nbsp;&nbsp;" . $submitLabel, array('type' => 'submit', 'class' => "btn pull-right  col-xs-12 $submitType")) !!}
            </div>

        </div>

        {!! Form::close() !!}

    </div>
    @include('forms._createErrors', ['errors' => $errors, 'formName' => $formName, 'formContainer' => 'addTopic'])
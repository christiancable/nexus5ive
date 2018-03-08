<div class="well">
    {!! 
        Form::open(array(
            'route' => ['topic.store'],
            'class' => 'form'
            )) !!}
        <?php
        $submitLabel = 'Add Topic';
        $submitIcon = 'glyphicon-plus-sign';
        $submitType = 'btn-primary';
        ?>
        {!! Form::hidden("section_id", $section->id) !!}
        {!! Form::hidden("secret", false) !!}  
        {!! Form::hidden("readonly", false) !!}

        <div class="form-group">    
            {!! Form::label("title",'Title', ['class' => 'hidden']) !!}
            {!! Form::text("title", null, ['class'=> 'form-control', 'placeholder' => 'Title'])!!}
        </div>
        <div class="form-group">
            {!! Form::label("intro",'Introduction', ['class' => 'hidden']) !!}
            {!! Form::textarea("intro", null, ['class'=> 'form-control', 'rows' => '3', 'placeholder' => 'Introduction'])!!}
        </div>

        <div class="row form-inline">

            <div class="col-md-2">
                <div class="checkbox">
                    <label>{!! Form::checkbox("secret")!!} Anonymous</label>
                </div>
                <div class="checkbox">
                    <label>{!! Form::checkbox("readonly")!!} Read Only</label>
                </div>
            </div>

            <div class="col-md-7 form-group">     
                <label>Order {!! Form::selectRange("weight", 0, 10, null, ['class' => 'form-control'])!!} </label>
            </div>

            <div class="col-md-3">
                {!! Form::button("<span class='glyphicon $submitIcon'></span>&nbsp;&nbsp;" . $submitLabel, array('type' => 'submit', 'class' => "btn pull-right  col-xs-12 $submitType")) !!}
            </div>

        </div>

        {!! Form::close() !!}

    </div>
    @if ($errors->topicCreate->all())
    @include('forms._createErrors', ['errors' => $errors->topicCreate->all(), 'formContainer' => 'addTopic'])
    @endif
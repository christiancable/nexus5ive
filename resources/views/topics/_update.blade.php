<div class="well">
    {!! 
        Form::open(
            array(
            'route' => ['topic.update', $topic->id],
            'class' => 'form',
            'method' => 'PATCH'
        )) 
    !!}
    <?php
        $formName = 'topicUpdate' . $topic->id;
        $submitLabel = 'Save Changes';
        $submitIcon = 'glyphicon-pencil';
        $submitType = 'btn-info';

    ?>
    {!! Form::hidden($formName . '[id]', $topic->id) !!}

    {!! Form::hidden($formName . '[secret]', false) !!}  
    {!! Form::hidden($formName. '[readonly]', false) !!}

    <div class="form-group">    
        {!! Form::label("title",'Title', ['class' => 'hidden']) !!}
        {!! Form::text($formName . '[title]', $topic->title, ['class'=> 'form-control', 'placeholder' => 'Title'])!!}
    </div>
    <div class="form-group">
        {!! Form::label("intro",'Introduction', ['class' => 'hidden']) !!}
        {!! Form::textarea($formName . "[intro]", $topic->intro, ['class'=> 'form-control', 'rows' => '3', 'placeholder' => 'Introduction'])!!}
    </div>
    
    <div class="row form-inline">

        <div class="col-md-2">
            <div class="checkbox">
                @if ($topic->secret)
                    <label>{!! Form::checkbox($formName . "[secret]", true, true)!!} Anonymous</label>
                @else
                    <label>{!! Form::checkbox($formName . "[secret]")!!} Anonymous</label>
                @endif
            </div>
            <div class="checkbox">
                @if ($topic->readonly)
                    <label>{!! Form::checkbox($formName . "[readonly]", true, true)!!} Read Only</label>
                @else
                    <label>{!! Form::checkbox($formName . "[readonly]")!!} Read Only</label>
                @endif 
            </div>
        </div>

        <div class="col-md-7 form-group">     
        @if(isset($moderatedSections))
            <label>Section {!! Form::select($formName . "[section_id]", $moderatedSections, $topic->section_id, ['class' => 'form-control'])!!} 
            </label>
        @endif
        <label>Order {!! Form::selectRange($formName . "[weight]", 0, 10, $topic->weight, ['class' => 'form-control'])!!} </label>
        </div>


        <div class="col-md-3">
                {!! Form::button("<span class='glyphicon $submitIcon'></span>&nbsp;&nbsp;" . $submitLabel, array('type' => 'submit', 'class' => "btn pull-right  col-xs-12 $submitType")) !!}
        </div>
    {!! Form::close() !!}
    </div>   

    <?php $errorBag = 'topicUpdate' . $topic->id ?>
    @if ($errors->$errorBag->all())
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
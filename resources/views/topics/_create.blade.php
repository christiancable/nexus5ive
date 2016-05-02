<div class="well">
    @if (isset($topic))
        {!! 
        Form::model($topic, array(
            'route' => ['topic.update', $topic->id],
            'class' => 'form',
            'method' => 'PATCH'
            )) !!}
        {!! Form::hidden('id', $topic->id) !!}
        <?php
        $formName = 'topic'.$topic->id;
        $submitLabel = 'Save Changes';
        $submitIcon = 'glyphicon-pencil';
        $submitType = 'btn-info';
        ?>
    @else 
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
        {!! Form::hidden('section_id', $section->id) !!}
    @endif
    {!! Form::hidden('secret', false) !!}  
    {!! Form::hidden('readonly', false) !!}

    <div class="form-group">    
        {!! Form::label('title','Title', ['class' => 'hidden']) !!}
        {!! Form::text('title', null, ['class'=> 'form-control', 'placeholder' => 'Title'])!!}
    </div>
    <div class="form-group">
        {!! Form::label('intro','Introduction', ['class' => 'hidden']) !!}
        {!! Form::textarea('intro', null, ['class'=> 'form-control', 'rows' => '3', 'placeholder' => 'Introduction'])!!}
    </div>
    
    <div class="row form-inline">

        <div class="col-md-2">
            <div class="checkbox">
                <label>{!! Form::checkbox('secret')!!} Anonymous</label>
            </div>
            <div class="checkbox">
                <label>{!! Form::checkbox('readonly')!!} Read Only</label>
            </div>
        </div>

        <div class="col-md-7 form-group">     
        @if(isset($moderatedSections))
            <label>Section {!! Form::select('section_id', $moderatedSections, null, ['class' => 'form-control'])!!} 
            </label>
        @endif
        <label>Order {!! Form::selectRange('weight', 0, 10, null, ['class' => 'form-control'])!!} </label>
        </div>


        <div class="col-md-3">
                {!! Form::button("<span class='glyphicon $submitIcon'></span>&nbsp;&nbsp;" . $submitLabel, array('type' => 'submit', 'class' => "btn pull-right  col-xs-12 $submitType")) !!}
        </div>
   
</div>

   
{!! Form::close() !!}

@if (Session::get('form') == $formName)
    @if ($errors->all())
    <div class="alert alert-warning" role="alert">
        <ul>
            @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif
@endif
</div>
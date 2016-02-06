<div class="well">

    @if (isset($topic))
        {!! 
        Form::model($topic, array(
            'route' => ['topic.update', $topic->id],
            'class' => 'form',
            'method' => 'PATCH'
            )) !!}
        {!! Form::hidden('id', $topic->id) !!}
        <?php $submitLabel = 'Save Changes' ?>
    @else 
        {!! 
        Form::open(array(
        'route' => ['topic.store'],
        'class' => 'form'
        )) !!}
        <?php $submitLabel = 'Add Topic' ?>
        {!! Form::hidden('section_id', $section->id) !!}
    @endif
    {!! Form::hidden('secret', false) !!}  
    {!! Form::hidden('readonly', false) !!}

    <div class="form-group">    
        {!! Form::label('title','Title') !!}
        {!! Form::text('title', null, ['class'=> 'form-control'])!!}
    </div>
    <div class="form-group">
        {!! Form::label('intro','Introduction') !!}
        {!! Form::textarea('intro', null, ['class'=> 'form-control', 'rows' => '3'])!!}
    </div>

    <div class="checkbox">
        <label>{!! Form::checkbox('secret')!!} Anonymous</label>
    </div>

    <div class="checkbox">
        <label>
            {!! Form::checkbox('readonly')!!} Read Only</label>
    </div>

    <div class="form-group">
        <label>Order {!! Form::selectRange('weight', 0, 10)!!} </label> 
    </div>

    <div class="form-group">
        {!! Form::submit($submitLabel, ['class'=> 'btn btn-warning form-control']) !!}
    </div>

    {!! Form::close() !!}

    @if ($errors->all())
    <div class="alert alert-warning" role="alert">
        <ul>
            @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif
</div>
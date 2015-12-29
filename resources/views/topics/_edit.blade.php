
<ul class="nav nav-tabs" id="topic{{$topic->id}}">
    <li role="presentation" class="active"><a href="#topic-view{{$topic->id}}">View</a></li>
    <li role="presentation"><a href="#topic-edit{{$topic->id}}">Settings</a></li>
</ul>

<div class="tab-content">
    <div role="tabpanel" class="tab-pane active" id="topic-view{{$topic->id}}">
        @include('topics._read', $topic)
    </div>
    <div role="tabpanel" class="tab-pane" id="topic-edit{{$topic->id}}">
     <div class="well">
{!! 
Form::model($topic, array(
    'route' => ['topic.update', $topic->id],
    'class' => 'form',
    'method' => 'PATCH'
    )) 
!!}

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
        {!! Form::submit('Save Changes', ['class'=> 'btn btn-warning form-control']) !!}
    </div>

{!! Form::close() !!}

            
    </div>
</div>
</div>
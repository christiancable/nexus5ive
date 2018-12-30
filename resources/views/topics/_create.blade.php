{!! 
    Form::open(array(
        'route' => ['topic.store'],
        'class' => 'form'
    )) 
!!}
{!! Form::hidden("section_id", $section->id) !!}
{!! Form::hidden("secret", false) !!}  
{!! Form::hidden("readonly", false) !!}

<div class="form-group">    
    {!! Form::label("title",'Title', ['class' => 'sr-only']) !!}
    {!! Form::text("title", null, ['class'=> 'form-control', 'placeholder' => 'Title'])!!}
</div>
<div class="form-group">
    {!! Form::label("intro",'Introduction', ['class' => 'sr-only']) !!}
    {!! Form::textarea("intro", null, ['class'=> 'form-control', 'rows' => '3', 'placeholder' => 'Introduction'])!!}
</div>

<div class="d-md-flex justify-content-md-between">
    <fieldset>
        <div class="form-check">
            <input class="form-check-input" type="checkbox" value="1" id="secret" name="secret">
            <label class="form-check-label" for="secret">Anonymous</label>
        </div>

        <div class="form-check">
            <input class="form-check-input" type="checkbox" value="1" id="readonly" name="readonly">
            <label class="form-check-label" for="readonly">Read Only</label>    
        </div>
    </fieldset>

    <div class="form-group form-inline">
        <label class="mr-sm-2" for="weight">Order</label>
            {!! Form::selectRange("weight", 0, 10, null, ['class' => 'form-control']) !!}
        </select>
    </div>
</div>

<div class="d-flex flex-row-reverse bd-highlight">    
        <div class="form-group ml-2">          
            {!! Form::button("<span class='oi oi-plus mr-2'></span>Add Topic",
                array(
                    'type'  => 'submit',
                    'class' => "btn btn-success"
                    )
            ) !!}
        </div>
</div>

{!! Form::close() !!}


@if ($errors->topicCreate->all())
@include('forms._errors', ['errors' => $errors->topicCreate->all(), 'formContainer' => 'addTopic'])
@endif
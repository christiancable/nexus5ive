<?php
$formName = 'topicUpdate' . $topic->id;
$errorBag = 'topicUpdate' . $topic->id;
?>

{!! 
    Form::open(
        array(
        'route' => ['topic.update', $topic->id],
        'class' => 'form',
        'method' => 'PATCH'
    )) 
!!}
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


    <div class="d-md-flex justify-content-md-between">

        <fieldset>
            <div class="form-check">
                @if ($topic->secret)
                    <input class="form-check-input" type="checkbox" value="1" id="{{$formName}}[secret]" name="{{$formName}}[secret]" checked>
                @else
                    <input class="form-check-input" type="checkbox" value="1" id="{{$formName}}[secret]" name="{{$formName}}[secret]">
                @endif
                <label class="form-check-label" for="{{$formName}}[secret]">Anonymous</label>
            </div>

            <div class="form-check">
                @if ($topic->readonly)
                <input class="form-check-input" type="checkbox" value="1" id="{{$formName}}[readonly]" name="{{$formName}}[readonly]" checked>
                @else
                <input class="form-check-input" type="checkbox" value="1" id="{{$formName}}[readonly]" name="{{$formName}}[readonly]">
                @endif 
                <label class="form-check-label" for="{{$formName}}[readonly]">Read Only</label>    
            </div>
        </fieldset>

        @if(isset($moderatedSections))
            <div class="form-group form-inline">
                <label class="mr-sm-2" for="{{$formName}}[section_id]">Section</label>
                    {!! Form::select($formName . "[section_id]", $moderatedSections, $topic->section_id, ['class' => 'form-control'])!!}
                </select>
            </div>
        @endif 

        <div class="form-group form-inline">
            <label class="mr-sm-2" for="{{$formName}}[weight]">Order</label>
                {!! Form::selectRange($formName . "[weight]", 0, 10, $topic->weight, ['class' => 'form-control'])!!}
            </select>
        </div>
    </div>


    <div class="d-flex flex-row-reverse bd-highlight">    
            <div class="form-group ml-2">          
                {!! Form::button("<span class='oi oi-pencil mr-2'></span>Save Changes",
                    array(
                        'type'  => 'submit',
                        'class' => "btn btn-success"
                        )
                ) !!}
            </div>
            {!! Form::close() !!}

            <form action="{{action('Nexus\TopicController@destroy', ['id' => $topic->id])}}" method="POST">
                <div class="form-group">
                    {{ csrf_field() }}
                    {{ method_field('DELETE') }}
                    {!! Form::button("<span class='oi oi-box mr-2'></span>Archive Topic",
                        array(
                            'type'  => 'submit',
                            'class' => "btn btn-warning"
                            )
                    ) !!}
                </div>
            {!! Form::close() !!}
    </div>

    @if ($errors->$errorBag->any())
        @include('forms._errors', ['errors' => $errors->$errorBag->all()])
    @endif
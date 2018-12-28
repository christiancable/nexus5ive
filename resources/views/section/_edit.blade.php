<?php
$errorBag = 'sectionUpdate' . $subSection->id;
$formName = 'section'.$subSection->id;
$submitLabel = 'Save Changes';
$submitIcon = 'glyphicon-pencil';
$submitType = 'btn-info';
?>

{{-- this is for moderators to edit sub sections --}}
<div class="card border-0 bg-transparent">
{!! Form::open(
    array(
    'route'     => ['section.update', $subSection->id],
    'class'     => 'form',
    'method'    => 'PATCH',
    'name'      => $formName,
)) !!}

    {!! Form::hidden("form[$formName][id]", $subSection->id) !!}

    <div class="form-group">
        {!! Form::text("form[$formName][title]", $subSection->title, ['class'=> 'form-control', 'placeholder'=>'Title']) !!}
    </div>

    <div class="form-group">
        {!! Form::textarea("form[$formName][intro]", $subSection->intro, ['class'=> 'form-control']) !!}
    </div>

    <div class="d-md-flex justify-content-md-between">

        <div class="form-group form-inline">
            <label class="mr-sm-2" for="{{$formName}}[user_id]">Moderator</label>
                {!!
                    Form::select(
                        "form[$formName][user_id]",
                        $potentialModerators,
                        $subSection->moderator->id,                    
                        ['class' => 'form-control']
                    )
                !!}
            </select>
        </div>

        <div class="form-group form-inline">
            <label class="mr-sm-2" for="{{$formName}}[parent_id]">Section</label>
                {!! 
                    Form::select(
                        "form[$formName][parent_id]",
                        $destinations->pluck('title','id')->toArray(),
                        $subSection->parent->id,
                        ['class' => 'form-control']
                    )
                !!}
            </select>
        </div>

        <div class="form-group form-inline">
            <label class="mr-sm-2" for="{{$formName}}[weight]">Order</label>
                {!!
                    Form::selectRange(
                        "form[$formName][weight]",
                        0,
                        10,
                        $subSection->weight,
                        ['class' => 'form-control']
                    )
                !!}
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

        <form action="{{action('Nexus\SectionController@destroy', ['id' => $subSection->id])}}" method="POST">
            <div class="form-group">
                {{ csrf_field() }}
                {{ method_field('DELETE') }}
                {!! Form::button("<span class='oi oi-box mr-2'></span>Archive Section",
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

</div>
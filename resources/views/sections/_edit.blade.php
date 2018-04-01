{{-- this is for moderators to edit sub sections --}}
<div class="panel panel-primary">
    <div class="well">

    <?php
    $errorBag = 'sectionUpdate' . $subSection->id;
    $formName = 'section'.$subSection->id;
    $submitLabel = 'Save Changes';
    $submitIcon = 'glyphicon-pencil';
    $submitType = 'btn-info';
    ?>

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

        <div class="row form-inline">

            <div class="col-xs-8 col-sm-4 col-lg-3 form-group">
                <label>Section 
                {!! 
                    Form::select(
                        "form[$formName][parent_id]",
                        $destinations->pluck('title','id')->toArray(),
                        $subSection->parent->id,
                        ['class' => 'form-control']
                    )
                !!}
                </label>
            </div>

            <div class="col-xs-12  col-sm-4 col-lg-4 form-group">
                <label> Moderator 
                {!!
                    Form::select(
                        "form[$formName][user_id]",
                        \App\User::all()->pluck('username', 'id')->toArray(),
                        $subSection->moderator->id,                    
                        ['class' => 'form-control']
                    )
                !!}
                </label>
            </div>

            <div class="col-sx-4 col-sm-4 col-lg-2 form-group">
                <label>Order
                {!!
                    Form::selectRange(
                        "form[$formName][weight]",
                        0,
                        10,
                        $subSection->weight,
                        ['class' => 'form-control']
                    )
                !!}
                </label>
            </div>

            <div class="col-xs-12 col-lg-3">
                {!!
                    Form::button(
                        "<span class='glyphicon glyphicon-pencil'></span>&nbsp;&nbsp;Save Changes",
                        array(
                            'type'  => 'submit',
                            'class' => "btn pull-right col-xs-12 btn-info", 
                            'value' => $formName
                        )
                    ) 
                !!}
                
            </div>
        
        </div>

    {!! Form::close() !!}

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

    </div> <!-- well -->
</div> <!-- panel panel-primary -->
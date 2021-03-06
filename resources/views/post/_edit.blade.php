<?php
    $formName = $post->id;
    $errorBag = 'postUpdate' . $post->id;
?>

<div class="mb-3">
    {!! Form::open(
        array(
            'route'     => ['posts.update', $post->id],
            'class'     => 'form',
            'method'    => 'PATCH',
            'name'      => $formName,
            )
    ) !!}
    {!! Form::hidden('id', $post->id) !!}

    <div class="form-group">
        {!! Form::text("form[$formName][title]", $post->title, ['class'=> 'form-control', 'placeholder'=>'Subject']) !!}
    </div>

    <div class="form-group">
        {!! Form::textarea("form[$formName][text]", $post->text, ['class'=> 'form-control']) !!}
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
            
            @if ($allowDelete)
                <form action="{{action('Nexus\PostController@destroy', ['post' => $post->id])}}" method="POST">
                    <div class="form-group">
                        @csrf
                        {{ method_field('DELETE') }}

                        {!! Form::button("<span class='oi oi-delete mr-2'></span>Delete",
                            array(
                                'type'  => 'submit',
                                'class' => "btn btn-danger"
                                )
                        ) !!}
                    </div>
                {!! Form::close() !!}
            @endif
    </div>
</div>


@if ($errors->$errorBag->any())
    @include('forms._errors', ['errors' => $errors->$errorBag->all()])
@endif 


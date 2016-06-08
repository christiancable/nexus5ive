<?php
    $formName = 'section'.$section->id;
?>
{!! Form::open(
    array(
        'route'     => ['section.update', $section->id],
        'class'     => 'form',
        'method'    => 'PATCH',
        'name'      => $formName,
        )
    ) 
!!}

{!! Form::hidden("form[$formName][id]", $section->id) !!}
{!! Form::hidden("form[$formName][parent_id]", $section->parent_id) !!}
{!! Form::hidden("form[$formName][current_section]", $section->id) !!}
{!! Form::hidden("form[$formName][user_id]", $section->user_id) !!}
{!! Form::hidden("form[$formName][weight]", $section->weight) !!}

<div class="form-group">
    {!! Form::text("form[$formName][title]", $section->title, ['class'=> 'form-control', 'placeholder'=>'Title']) !!}
</div>

<div class="form-group">
    {!! Form::textarea("form[$formName][intro]", $section->intro, ['class'=> 'form-control']) !!}
</div>
<?php
    $submitLabel = 'Save Changes';
    $submitIcon = 'glyphicon-pencil';
    $submitType = 'btn-info';
?>


<div class="row">    
    <div class="col-sm-12">
        <div class="form-group">          
        {!! 
            Form::button("<span class='glyphicon glyphicon-pencil'></span>&nbsp;&nbsp;Save Changes",
                array(
                    'type'  => 'submit',
                    'class' => "btn pull-right btn-info col-xs-12 col-sm-3", 
                    'value' => $formName
                    )
                ) 
        !!}
        </div>
    </div>
</div>

{!! Form::close() !!}

    @if ($message = Session::get('headerAlert'))
<div class="container">
    <div class="alert alert-info" role="alert">
        {!! Nexus\Helpers\NxCodeHelper::nxDecode($message) !!}
    </div>
</div>
@endif


    @if (session('alert'))
    <div class="container">
        <div class="alert alert-warning" role="alert">No updated topics found. Why not start a new conversation or read more sections?</div>
    </div>
    @endif 

   
    
 @if (session('topic'))
    <div class="container">
        <div class="alert alert-success" role="alert">
        
        <p>People have been talking! New posts found in <strong><a href="{{ action('Nexus\TopicController@show', ['topic_id' => session('topic')->id])}}"> {{session('topic')->title}}</a></strong><p>
        
        <p>Seeing too many old topics then <strong><a href="{{ action('Nexus\TopicController@markAllSubscribedTopicsAsRead')}}">mark all subscribed topics as read.</a></strong></p>
        </div>
    </div>
    @endif 




@if (Session::get('form') == $formName)
@if ($errors->any())
    <br/>
    <div class="row">
    <div class="col-sm-12">
        <p class="alert alert-danger">
       
        You need to <strong>give your section a title</strong>. Otherwise; <em>chaos</em>.
        </p>
        </div>
    </div>
@endif 
@endif



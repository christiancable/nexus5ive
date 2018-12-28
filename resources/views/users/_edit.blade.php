@if ($errors->userUpdate->any())
    @include('forms._createErrors', ['errors' => $errors->userUpdate->all()])
@endif
{!! 
Form::model($user, array(
    'route' => ['users.update', $user->username],
    'class' => 'form',
    'method' => 'PATCH'
    )) 
!!}

{!! Form::hidden('id', $user->id) !!}

 <div class="form-row">
    <div class="form-group col-md-6">
        {!! Form::label('name','Name') !!}
        {!! Form::text('name', null, ['class'=> 'form-control'])!!}
    </div>
    <div class="form-group col-md-6">
        {!! Form::label('location','Location') !!}
        {!! Form::text('location', null, ['class'=> 'form-control'])!!}
    </div>
  </div>

 <div class="form-row">
    <div class="form-group col-md-6">
        {!! Form::label('popname','Popname') !!}
        {!! Form::text('popname', null, ['class'=> 'form-control'])!!}
    </div>
    <div class="form-group col-md-6">
        {!! Form::label('favouriteMovie','Favourite Film') !!}
        {!! Form::text('favouriteMovie', null, ['class'=> 'form-control'])!!}
    </div>
</div>

<div class="form-row">
    <div class="form-group col-md-6">
        {!! Form::label('email','Email') !!}
        {!! Form::email('email', null, ['class'=> 'form-control', 'autocomplete' => 'off'])!!}
    </div>
    <div class="form-group col-md-6">
        {!! Form::label('favouriteMusic','Favourite Band') !!}
        {!! Form::text('favouriteMusic', null, ['class'=> 'form-control'])!!}
    </div>
</div>

<div class="form-row">
    <div class="form-check">
        {!! Form::hidden('private', false) !!}
        {!! Form::checkbox('private', true, $user->private, ['class' => 'form-check-input', 'id' => 'private'])!!}
        {!! Form::label('private','Hide Email', ['class' => 'form-check-label']) !!}
    </div>
</div>

<hr>


<div class="form-row form-inline">
    <div class="form-group col-12 col-md-6 ">
        {!! Form::label('theme','Theme', ['class'=>'mr-3']) !!}
        {!! Form::select('theme_id', $themes, $user->theme->id, ['class'=> 'form-control'])!!}
    </div>
    <div class="form-group col-12 col-md-6">
        <div class="form-check">
            {!! Form::hidden('viewLatestPostFirst', false) !!}
            {!! Form::checkbox('viewLatestPostFirst', true, $user->viewLatestPostFirst, ['class' => 'form-check-input', 'id' => 'viewLatestPostFirst'])!!}
            {!! Form::label('viewLatestPostFirst', 'Show Latest Posts First', ['class' => 'form-check-label']) !!}
        </div>
    </div>
</div>


<hr>

<div class="row mb-3">
    <div class="col">
        <div class="form-group">
            {!! Form::label('password','Password', ['class' => 'd-block']) !!}
            {!! Form::password('password', null, ['class'=> 'form-control'])!!}
        </div>
        <div class="form-group">
            {!! Form::label('password_confirmation','Confirm Password', ['class' => 'd-block']) !!}
            {!! Form::password('password_confirmation', null, ['class'=> 'form-control'])!!}
        </div>
    </div>

    <div class="col">
        @include('users._score', $user)
    </div>
</div>

<div class="form-group">
 {!! Form::label('about','About') !!}
{!! Form::textarea('about', null, ['class'=> 'form-control']) !!}
</div>
       
<div class="form-group">
    {!! Form::submit('Save Changes', ['class'=> 'btn btn-warning form-control']) !!}
</div>
{!! Form::close() !!}

@if (count($user->sections))
    <span>You moderate the following sections </span>
    <!-- Single button -->
    <div class="btn-group">
        <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            Choose Section <span class="caret"></span>
        </button>
        <ul class="dropdown-menu">
            @foreach ($user->sections as $section)
                <li><a href="{{ action('Nexus\SectionController@show', ['section_id' => $section->id]) }}">{{$section->title}}</a></li>
            @endforeach
        </ul>
    </div>
    <hr> 
@endif
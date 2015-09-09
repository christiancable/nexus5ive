@extends('layouts.master')

@section('content')
<div class="col-md-6">



{!! Form::open(array('url' => '/password/reset', 'class' => 'form')) !!}

    <input type="hidden" name="token" value="{{ $token }}">
    {!! csrf_field() !!}

<h1>Reset Your Password</h1>

@if (count($errors) > 0)
  <div class="alert alert-danger">
    There were some problems resetting your password:
    <br />
    <ul>
        @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
        @endforeach
    </ul>
  </div>
@endif

<div class="form-group">
  {!! Form::label('email', 'Your E-mail Address') !!}
  {!! Form::text('email', null, 
    array('class'=>'form-control', 'placeholder'=>'E-mail')) !!}
</div>

<div class="form-group">
 {!! Form::label('password', 'New Password') !!}
 {!! Form::password('password', 
   array('class'=>'form-control', 'placeholder'=>'Password')) !!}
</div>

<div class="form-group">
 {!! Form::label('password_confirmation', 'Confirm Password') !!}
 {!! Form::password('password_confirmation', 
   array('class'=>'form-control', 'placeholder'=>'Password')) !!}
</div>

<div class="form-group">
  {!! Form::submit('Reset Password', 
    array('class'=>'btn btn-primary')) !!}
</div>
{!! Form::close() !!}
</div>
@endsection
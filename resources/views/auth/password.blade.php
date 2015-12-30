@extends('layouts.master')

@section('content')
<div class="container">
  <div class="col-md-6">

    {!! Form::open(array('url' => '/password/email', 'class' => 'form')) !!}

    <h1>Recover Your Password</h1>

    @if (count($errors) > 0)
    <div class="alert alert-danger">
      There were some problems recovering your password:
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
      {!! Form::submit('E-mail Password Reset Link', 
      array('class'=>'btn btn-primary')) !!}
    </div>
    {!! Form::close() !!}
    @if (env('NEXUS_ADMIN_EMAIL'))
    <a href="mailto:{{env('NEXUS_ADMIN_EMAIL')}}?subject=Password Help!">Forgotten your username or using a different email address?</a>
    @endif
</div> <!-- .container -->
  </div>
</div>
@endsection
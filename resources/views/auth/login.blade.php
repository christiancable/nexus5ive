@extends('layouts.master')

@section('meta')
<title>{{config('nexus.name')}} - Login</title>
@endsection

@section('content')

<div class="container my-1">

<div class="card">
<div class="row card-body">
  <div class="col-lg">

    <form class="form" role="form" method="POST" action="{{ url('/login') }}">
    @csrf
  
    @if (count($errors) > 0)
    <div class="alert alert-danger">
      There were some problems signing into your account:
      <ul>
      @foreach ($errors->all() as $error)
        <li>{{ $error }}</li>
      @endforeach
      </ul>
    </div>
    @endif

    <div class="form-group">
      {!! Form::label('username', 'Username', ['class' => 'sr-only']) !!}
      <div class="input-group">
        <div class="input-group-prepend">
          <div class="input-group-text"><span class="oi oi-person"></span></div>
        </div>
        {!! Form::text('username', null, ['class'=>'form-control', 'placeholder'=>'Username']) !!}
      </div>
    </div>

    <div class="form-group">
      {!! Form::label('Password', 'password', ['class' => 'sr-only']) !!}
      <div class="input-group">
        <div class="input-group-prepend">
          <div class="input-group-text"><span class="oi oi-key"></span></div>
        </div>
        {!! Form::password('password', ['class'=>'form-control', 'placeholder'=>'Password']) !!}
      </div>
    </div>

    <div class="form-group">
      {!! Form::submit('Log In', array('class'=>'btn btn-primary')) !!}
    </div>

    <div class="form-group">
      <div class="form-check">
      {!! Form::checkbox('remember', true, null, ['class' => 'form-check-input', 'id' => 'remember']) !!}
      {!! Form::label('remember','Remember me', ['class' => 'form-check-label']) !!}
      </div>
    </div>
    {!! Form::close() !!}
  </div> <!-- .col-md -->
  
  <div class="col-lg">
    <p class="lead">There are those who believe that <strong>spodding</strong> here began out there, far across the network, with tribes of users who may have been the forefathers of the <em>Prestoneites</em>, or the <em>Facebookers</em>, or the <em>Twitters</em>.</p>
    <p>That they may have been the architects of the great forums, or the lost civilizations of <em>Monochrome</em> or <em>anonyMUD</em>. Some believe that there may yet be brothers of man who even now fight to survive somewhere beyond the screen&hellip;</p>
  </div> <!-- .col-md -->

</div> <!-- .row -->

    

<div class="text-center">    
  <p><a href="{{ url('/password/reset') }}">Forgot Your Password?</a></p>
</div> 
      </div>
</div> <!-- .container -->
@endsection

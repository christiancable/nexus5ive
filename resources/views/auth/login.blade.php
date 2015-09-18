@extends('layouts.master')
 
 @section('content')

 <div class="container">


 <div class="col-md-6">
 
 {!! Form::open(array('url' => '/auth/login', 'class' => 'form')) !!}
 
   <h1>Login</h1>
 
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
     {!! Form::label('username', 'Username') !!}
     {!! Form::text('username', null, 
       array('class'=>'form-control', 'placeholder'=>'username')) !!}
   </div>
 
   <div class="form-group">
     {!! Form::label('Password') !!}
     {!! Form::password('password', 
       array('class'=>'form-control', 'placeholder'=>'Password')) !!}
    </div>
 
   <div class="form-group">
     <label>
       {!! Form::checkbox('remember', 'remember') !!} Remember Me
     </label>
   </div>
 
   <div class="form-group">
     {!! Form::submit('Login', array('class'=>'btn btn-primary')) !!}
   </div>
 
   <a href="/password/email">Forgot Your Password?</a>
   </div>
   </div>
 {!! Form::close() !!}
 
 </div>

 </div>
 @endsection

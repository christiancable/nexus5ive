@extends('layouts.master')

@section('content')

<div class="container">

<div class="content">
  <h1>{{env('NEXUS_NAME')}}</h1>
  <p class="lead">hell is other people</p>
</div>
<hr/>

{!! Form::open(array('url' => '/auth/register', 
'class' => 'form')) !!}
<div class="row">

<div class="col-md-6">


@if (count($errors) > 0)
<div class="alert alert-danger">
 There were some problems creating an account:
 <ul>
   @foreach ($errors->all() as $error)
     <li>{{ $error }}</li>
   @endforeach
 </ul>
</div>
@endif

<div class="form-group">
 {!! Form::label('Username') !!}
 {!! Form::text('username', null, array('class'=>'form-control', 'placeholder'=>'Username')) !!}
</div>

<div class="form-group">
 {!! Form::label('Your E-mail Address') !!}
 {!! Form::text('email', null, 
   array(
   'class'=>'form-control', 
   'placeholder'=>'Email Address')
   ) !!}
</div>
<div class="form-group">
 {!! Form::label('Your Password') !!}
 {!! Form::password('password', 
   array('class'=>'form-control', 'placeholder'=>'Password')) !!}
</div>
<div class="form-group">
 {!! Form::label('Confirm Password') !!}
 {!! Form::password('password_confirmation', 
   array(
     'class'=>'form-control', 
     'placeholder'=>'Confirm Password')
   ) !!}
</div>

<div class="form-group">
 {!! Form::submit('Create My Account!', 
   array('class'=>'btn btn-primary')) !!}
</div>
{!! Form::close() !!}
</div>

<div class="col-md-6">

    <p class="lead">There are those who believe that <strong>spodding</strong> here began out there, far across the network, with tribes of users who may have been the forefathers of the <em>Prestoneites</em>, or the <em>Facebookers</em>, or the <em>Twitters</em>.</p>

    <p>That they may have been the architects of the great forums, or the lost civilizations of <em>Monochrome</em> or <em>anonyMUD</em>. Some believe that there may yet be brothers of man who even now fight to survive somewhere beyond the screen&hellip;</p>

  </div>

</div>




</div>
@endsection
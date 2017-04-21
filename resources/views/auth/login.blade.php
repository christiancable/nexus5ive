@extends('layouts.master')

@section('meta')
<title>{{config('nexus.name')}} - Login</title>
@endsection

@section('content')

<div class="container">

  <div class="content">
    <h1>{{config('nexus.name')}}</h1>
@if (config('nexus.special_event') === 'halloween')
   @include('special.halloween._loginHeading')
@else
  <p class="lead">when time becomes a loop</p>
@endif 
  </div>
  <hr/>


  <div class="row">
    <div class="col-md-6">

     <form class="form" role="form" method="POST" action="{{ url('/login') }}">
                        {{ csrf_field() }}

     {{-- {!! Form::open(array('url' => '/auth/login', 'class' => 'form')) !!} --}}

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

     
     {!! Form::close() !!}
   </div>

   <div class="col-md-6">

@if (config('nexus.special_event') === 'halloween')
  @include('special.halloween._login')
@else
<p class="lead">There are those who believe that <strong>spodding</strong> here began out there, far across the network, with tribes of users who may have been the forefathers of the <em>Prestoneites</em>, or the <em>Facebookers</em>, or the <em>Twitters</em>.</p>

    <p>That they may have been the architects of the great forums, or the lost civilizations of <em>Monochrome</em> or <em>anonyMUD</em>. Some believe that there may yet be brothers of man who even now fight to survive somewhere beyond the screen&hellip;</p>
@endif
  </div>
</div> <!-- .row -->
 <a href="{{ url('/password/reset') }}">Forgot Your Password?</a>
<hr/>

</div> <!-- .container -->

</div>

</div>
@endsection

@extends('layouts.master')

@section('content')
<div class="container">
  <div class="col-md-6">

    <form action="/password/email" method="POST" class="form">

    <h1>Recover Your Password</h1>

    @if (count($errors) > 0)
    <div class="alert alert-danger">
      There were some problems recovering your password:<br />
      <ul>
        @foreach ($errors->all() as $error)
        <li>{{ $error }}</li>
        @endforeach
      </ul>
    </div>
    @endif

    <div class="form-group">
      <label for="email">Your E-mail Address</label>
      <input type="text" name="email" class="form-control" placeholder="E-mail">
    </div>

    <div class="form-group">
      <button type="submit" class="btn btn-primary">E-mail Password Reset Link</button>
    </div>
    </form>

    @if (config('nexus.admin_email'))
    <a href="mailto:{{config('nexus.admin_email')}}?subject=Password Help!">Forgotten your username or using a different email address?</a>
    @endif
  </div> <!-- .container -->
</div>
@endsection

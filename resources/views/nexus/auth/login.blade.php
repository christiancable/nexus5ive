@extends('nexus.layouts.master')

@section('meta')
    <title>{{ config('nexus.name') }} - Login</title>
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

                        <div class="mb-3">
                            <label for="username" class="visually-hidden">Username</label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <div class="input-group-text">
                                        <x-heroicon-s-user class="icon_mini" aria-hidden="true" />
                                    </div>
                                </div>
                                <input type="text" name="username" class="form-control" placeholder="Username">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="password" class="visually-hidden">Password</label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <div class="input-group-text">
                                        <x-heroicon-s-key class="icon_mini" aria-hidden="true" />
                                    </div>
                                </div>
                                <input type="password" name="password" class="form-control" placeholder="Password">
                            </div>
                        </div>

                        <div class="mb-3">
                            <button type="submit" class="btn btn-primary">Log In</button>
                        </div>

                        <div class="mb-3">
                            <div class="form-check">
                                <input type="checkbox" name="remember" id="remember" class="form-check-input">
                                <label for="remember" class="form-check-label">Remember me</label>
                            </div>
                        </div>
                    </form>
                </div> <!-- .col-md -->


                <div class="col-lg">
                    @if ($mode ?? false)
                        {!! App\Helpers\NxCodeHelper::nxDecode($mode->welcome) !!}
                    @endif
                </div> <!-- .col-md -->

            </div> <!-- .row -->



            <div class="text-center">
                <p><a href="{{ route('password.request') }}">Forgot Your Password?</a></p>
            </div>
        </div>
    </div> <!-- .container -->
@endsection

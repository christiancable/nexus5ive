@extends('nexus.layouts.unverified')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">{{ __('Verify Your Email Address') }}</div>

                    <div class="card-body">

                        {{ __('Thanks for signing up! Before getting started, could you verify your email address by clicking on the link we just emailed to you? If you didn\'t receive the email, we will gladly send you another.') }}

                        @if (session('status') == 'verification-link-sent')
                            <div class="alert alert-success mt-2" role="alert">
                                {{ __('A new verification link has been sent to the email address you provided during registration.') }}
                            </div>
                        @endif


                        <div class="mt-4">

                            <div class="d-flex justify-content-between">
                                <form method="POST" action="{{ route('verification.send') }}">
                                    @csrf
                                        <button type="submit" class="btn btn-link p-0 m-0 align-baseline">
                                            {{ __('Resend Verification Email') }}
                                        </button>
                                </form>

                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="btn btn-link p-0 m-0 align-baseline">
                                        {{ __('Log Out') }}
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@extends('nexus.layouts.master')

@section('meta')
    <title>{{ config('nexus.name') }} - Reset your password</title>
@endsection

@section('content')
    <div class="container my-3">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header text-white bg-warning">{{ __('Reset Password') }}</div>

                    <div class="card-body">
                        {{ __('Forgot your password? Enter your email and we’ll send you a reset link.') }}

                        <!-- Session Status -->
                        @if (session('status') == 'We have emailed your password reset link.')
                            <div class="alert alert-success mt-2" role="alert">
                                <x-auth-session-status :status="session('status')" />
                            </div>
                        @endif

                        <div class="d-flex justify-content-center align-items-center my-2">
                            <form class="mt-2 w-100" method="POST" action="{{ route('password.email') }}">
                                @csrf
                                <!-- Email Address -->
                                <div class="row justify-content-center align-items-center mb-3">
                                    <div class="col-auto text-end">
                                        <x-input-label for="email" :value="__('E-mail Address')" class="mb-0" />
                                    </div>
                                    <div class="col-md-5">
                                        <x-text-input id="email" class="form-control mt-1" type="email" name="email"
                                            :value="old('email')" required autofocus />
                                        <x-input-error :messages="$errors->get('email')" class="mt-2" />
                                    </div>
                                </div>
                                <div class="d-flex justify-content-center mt-4">
                                    <x-primary-button>
                                        {{ __('Send Password Reset Link') }}
                                    </x-primary-button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                @if (config('nexus.admin_email'))
                    <div class="text-center mt-3">
                        <a href="mailto:{{ config('nexus.admin_email') }}?subject=Password Help!">Forgotten your
                            username or using a different email address?</a>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection

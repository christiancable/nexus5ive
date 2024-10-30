@extends('nexus.layouts.master')

@section('meta')
    <title>{{ config('nexus.name') }} - Reset your password</title>
@endsection

@section('content')
    <div class="container my-3">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header text-white bg-danger">{{ __('Reset Password') }}</div>

                    <div class="card-body">
                        {{ __('Forgot your password? No problem. Just let us know your email address and we will email you a password reset link that will allow you to choose a new one.') }}

                        <!-- Session Status -->
                        @if (session('status') == 'We have emailed your password reset link.')
                            <div class="alert alert-success mt-2" role="alert">
                                <x-auth-session-status :status="session('status')" />
                            </div>
                        @endif

                        <form class="mt-2" method="POST" action="{{ route('password.email') }}">
                            @csrf
                            <!-- Email Address -->
                            <div>
                                <x-input-label for="email" :value="__('Email')" />
                                <x-text-input id="email" class="block mt-1 w-full" type="email" name="email"
                                    :value="old('email')" required autofocus />
                                <x-input-error :messages="$errors->get('email')" class="mt-2" />
                            </div>

                            <div class="flex items-center justify-end mt-4">
                                <button type="submit" class="btn btn-link p-0 m-0 align-baseline">
                                    <x-primary-button>
                                        {{ __('Email Password Reset Link') }}
                                    </x-primary-button>
                            </div>
                        </form>

                        @if (config('nexus.admin_email'))
                            <div class="text-center mt-3">
                                <a href="mailto:{{ config('nexus.admin_email') }}?subject=Password Help!">Forgotten your
                                    username or using a different email address?</a>
                            </div>
                        @endif

                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

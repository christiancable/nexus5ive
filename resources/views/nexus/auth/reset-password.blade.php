@extends('nexus.layouts.master')

@section('meta')
    <title>Reset Password</title>
@endsection

@section('content')
    <div class="container mt-5" style="max-width: 400px;">
        <h2 class="mb-4 text-center">Reset Password</h2>
        <form method="POST" action="{{ route('password.store') }}">
            @csrf
            @method('POST')
            <input type="hidden" name="token" value="{{ request()->route('token') }}">

            <div class="mb-3">
                <label for="email" class="form-label">Email Address</label>
                <input id="email" type="email" class="form-control @error('email') is-invalid @enderror"
                    name="email" value="{{ old('email', request('email')) }}" required autofocus>
                @error('email')
                    <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                @enderror
            </div>

            <div class="mb-3">
                <label for="password" class="form-label">New Password</label>
                <input id="password" type="password" class="form-control @error('password') is-invalid @enderror"
                    name="password" required>
                @error('password')
                    <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                @enderror
            </div>

            <div class="mb-3">
                <label for="password-confirm" class="form-label">Confirm Password</label>
                <input id="password-confirm" type="password" class="form-control" name="password_confirmation" required>
            </div>

            <div class="d-grid">
                <button type="submit" class="btn btn-primary">Reset Password</button>
            </div>
        </form>
    </div>
@endsection

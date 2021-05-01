@extends('layouts.unverified')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">User Account Inactive</div>

                <div class="card-body">
                   
                    <div class="col-lg">
                        <p>Your account is currently inactive.</p>
                        @if (config('nexus.admin_email'))
                            <p><a href="mailto:{{config('nexus.admin_email')}}?subject=Legacy Nexus Account">If you believe this to be in error than please email for help.</a></p>
                        @endif
                    </div>
                   
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

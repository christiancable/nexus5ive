@extends('layouts.master')

@section('meta')
<title>Who is Online</title>
@endsection

@section('content')

<div class="container">


    <div class="content">
        <h1>Who is Online</h1>
        <p class="lead">Hell is other people</p>
    </div>

    <hr>

    <div class="content">
        @if (count($activities))
        <ul>
            @foreach ($activities as $activity)
            @include('activities._read', $activity)
            @endforeach
        </ul>
        @else 
        <div class="content">
            <div class="alert alert-warning" role="alert">Looks like no one is here. But *you* are here. How odd. It's a bit quiet isn't it?</div>
        </div>
        @endif
    </div>

    <hr>
    <div class="content">
    <small class="text-info">Based on user activity from the last {{env('NEXUS_RECENT_ACTIVITY')}} minutes.</small>
    </div>

</div>
@endsection

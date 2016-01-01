@extends('layouts.master')

@section('meta')
<title>Active Users</title>
@endsection

@section('content')

<div class="container">


    <div class="content">
        <h1>Active Users</h1>
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
        @endif
    </div>


</div>
@endsection

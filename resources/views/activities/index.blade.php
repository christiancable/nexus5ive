@extends('layouts.master')

@section('meta')
<title>Who is Online</title>
@endsection

@section('breadcrumbs')
@include('_breadcrumbs', $breadcrumbs)
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
        <table class="table table-striped table-condensed">
            <thead>
                <tr>
                    <th></th>
                    <th>Name</th>
                    <th class="hidden-xs">Popname</th>
                    <th>Current Action</th>
                    <th>Last Active</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($activities as $activity)
                @include('activities._read', $activity)
                @endforeach
            </tbody>
        </table>
        @else 
        <div class="content">
            <div class="alert alert-warning" role="alert">Looks like there's no one else here. But *you* are here. How odd. (╯°□°）╯︵ ┻━┻</div>
        </div>
        @endif
    </div>

    <hr>
    <div class="content">
    <small class="text-info">Based on activity from the last {{$activityWindow->diffForHumans(null,true)}}.</small>
    </div>

</div>
@endsection

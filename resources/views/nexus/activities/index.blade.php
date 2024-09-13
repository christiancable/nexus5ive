@extends('nexus.layouts.master')

@section('meta')
<title>Who is Online</title>
@endsection

@section('breadcrumbs')
@include('nexus._breadcrumbs', $breadcrumbs)
@endsection 

@section('content')

<div class="container">

    @include('nexus.shared._heading', [$heading = 'Who is Online', $lead = 'Hell is other people', $icon = 'globe'])

    <div>
        @if (count($activities))
            <table class="table table-striped table-borderless">
                <thead>
                    <tr>
                        <th></th>
                        <th>Name</th>
                        <th class="d-none d-sm-table-cell">Popname</th>
                        <th>Current Action</th>
                        <th>Last Active</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($activities as $activity)
                    @include('nexus.activities._read', $activity)
                    @endforeach
                </tbody>
            </table>
        @else 
            <div>
                <div class="alert alert-warning" role="alert">Looks like there's no one else here. But *you* are here. How odd. (╯°□°）╯︵ ┻━┻</div>
            </div>
        @endif
    </div>

    <hr>

    <div>
        <small class="text-info">Based on activity from the last {{$activityWindow->diffForHumans(null,true)}}.</small>
    </div>

</div>
@endsection

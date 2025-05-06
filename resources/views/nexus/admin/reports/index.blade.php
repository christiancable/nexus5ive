@extends('nexus.layouts.master')

@section('meta')
    <title>Moderation Queue</title>
@endsection

@section('breadcrumbs')
    @include('nexus._breadcrumbs', $breadcrumbs)
@endsection

@section('content')
    <div class="container my-4">

        <x-heading heading="Moderation Queue">
            <x-slot:icon>
                <x-heroicon-s-flag class="icon_large mr-1" aria-hidden="true" />
            </x-slot>
        </x-heading>


        @foreach ($reports as $report)
            <x-content-report :report="$report" />
        @endforeach

        <div>
            {{ $reports->links() }}
        </div>
    </div>
@endsection

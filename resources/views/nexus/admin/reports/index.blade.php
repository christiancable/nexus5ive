@extends('nexus.layouts.master')

@section('meta')
    <title>Moderation</title>
@endsection

@section('breadcrumbs')
    @include('nexus._breadcrumbs', $breadcrumbs)
@endsection

@section('content')
    <div class="container my-4">

        <x-heading heading="Moderation">
            <x-slot:icon>
                <x-heroicon-s-flag class="icon_large mr-1" aria-hidden="true" />
            </x-slot>
        </x-heading>

        <ul class="nav nav-pills mb-3">
            <li class="nav-item">
                <a class="nav-link {{ request('status') ? '' : 'active' }}" href="{{ route('reports.index') }}">All</a>
            </li>
            @foreach (\App\Models\Report::STATUSES as $key => $label)
                <li class="nav-item">
                    <a class="nav-link {{ request('status') === $key ? 'active' : '' }}"
                        href="{{ route('reports.index', ['status' => $key]) }}">
                        {{ $label }}
                        <span class="badge bg-light text-secondary">
                            {{ $totals[$key] ?? 0 }}
                        </span>
                    </a>
                </li>
            @endforeach
        </ul>



        @foreach ($reports as $report)
            <x-content-report :report="$report" />
        @endforeach

        <div>
            {{ $reports->links() }}
        </div>
    </div>
@endsection

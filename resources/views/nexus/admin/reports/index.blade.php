@extends('nexus.layouts.master')

@section('meta')
    <title>Moderation Queue</title>
@endsection

@section('breadcrumbs')
    @include('nexus._breadcrumbs', $breadcrumbs)
@endsection

@section('content')
    <div class="container">
        <h1 class="mb-4">Open Reports</h1>

        <table class="table table-striped table-hover align-middle">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Content</th>
                    <th>Reason</th>
                    <th>Reporter</th>
                    <th>Status</th>
                    <th>Reported</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($reports as $report)
                    <tr>
                        <td>{{ $report->id }}</td>

                        <td>
                            @if ($report->reportable)
                                <strong>{{ class_basename($report->reportable_type) }}
                                    #{{ $report->reportable->id }}</strong><br>

                                    <div class="border rounded p-2 bg-light text-muted fst-italic small">
                                        {{ Str::limit($report->snapshot_text, 150) }}
                                    </div>

                                    @if ($report->reportable_link)
                                    <a href="{{$report->reportable_link }}">View in context</a>
                                    @endif
                            @else
                                <em>Content no longer available</em>
                            @endif
                        </td>

                        <td>{{ $report->reason_label }}</td>

                        <td>
                            @if($report->reporter)
                            <x-profile-link :user="$report->reporter" />
                            @else
                            Anonymous
                            @endif
                        </td>

                        <td>
                            <span class="badge {{ $report->statusBadgeClass }}">
                                {{ $report->status_label }}
                            </span>
                        </td>

                        <td>{{ $report->created_at->diffForHumans() }}</td>

                        <td>
                            {{-- <a href="{{ route('moderation.report.view', $report->id) }}" class="btn btn-primary btn-sm">View</a> --}}
                            <a href="" class="btn btn-primary btn-sm">View</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center">No open reports.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <div class="d-flex justify-content-center">
            {{ $reports->links() }}
        </div>
    </div>
@endsection

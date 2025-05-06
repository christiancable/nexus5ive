@extends('nexus.layouts.master')

@section('meta')
    <title>Moderating Report</title>
@endsection

@section('breadcrumbs')
    @include('nexus._breadcrumbs', $breadcrumbs)
@endsection

@section('content')

    <div class="container">

        {{-- Report card --}}
        <x-content-report :report="$report" :preview="false" />

        {{-- Previous notes/history --}}
        @if ($report->moderationNotes->count())
            <div class="card shadow-sm mb-3 bg-light p-3">
                <h5 class="card-title mb-3">
                    Moderation Log
                </h5>

                @foreach ($report->moderationNotes as $note)
                    <p class="mb-2 small text-muted">
                        @if ($note->user)
                            <x-profile-link :user="$note->user" />
                        @else
                            <strong>{{ $note->user_name ?? 'Deleted user' }}</strong>
                        @endif
                        &middot; {{ $note->created_at->diffForHumans() }}
                    </p>
                    <blockquote class="blockquote mb-4 fs-6">
                        {{ $note->note }}
                    </blockquote>
                @endforeach
            </div>
        @endif

        {{-- Admin action form --}}
        <div class="card shadow-sm mb-3">
            <div class="card-header">
                Moderation Actions
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('reports.update', $report) }}">
                    @csrf
                    @method('PUT')

                    <div class="mb-3">
                        <label for="status" class="form-label">Change Status</label>
                        <select name="status" id="status" class="form-select">
                            @foreach (\App\Models\Report::STATUSES as $value => $label)
                                <option value="{{ $value }}" @if ($report->status === $value) selected @endif>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="moderator_note" class="form-label">Add Moderator Note</label>
                        <textarea name="moderator_note" id="moderator_note" class="form-control" rows="3"
                            placeholder="Describe the action taken or any relevant notes"></textarea>
                    </div>

                    <x-ui.button type="submit" variant="primary">
                        Update Report
                    </x-ui.button>
                </form>
            </div>
        </div>
    </div>

@endsection

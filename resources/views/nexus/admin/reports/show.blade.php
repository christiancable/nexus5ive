@extends('nexus.layouts.master')

@section('meta')
    <title>Moderate Report</title>
@endsection

@section('breadcrumbs')
    @include('nexus._breadcrumbs', $breadcrumbs)
@endsection

@section('content')

<div class="container">

    {{-- Report card --}}
    <x-content-report :report="$report" :preview="false"/>
    
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

    {{-- Previous notes/history --}}
    @if ($report->notes && count($report->notes))
    <div class="card shadow-sm">
        <div class="card-header">
            Moderator Notes History
        </div>
        <div class="card-body">
            <ul class="list-group list-group-flush">
                @foreach ($report->notes as $note)
                    <li class="list-group-item">
                        <strong>{{ $note->created_at->diffForHumans() }}:</strong> {{ $note->content }}
                    </li>
                @endforeach
            </ul>
        </div>
    </div>
    @endif

</div>

@endsection

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
    <div class="card mb-3 shadow-sm">
        <div class="card-body d-flex flex-column flex-md-row justify-content-between">
            <div class="flex-grow-1">
                <h5 class="card-title mb-1">
                    Report <span class="text-muted">#{{ $report->id }}</span> —
                    <span class="badge {{ $report->statusBadgeClass }}">
                        {{ $report->status_label }}
                    </span>
                </h5>

                <p class="mb-2 text-muted small">
                    {{ class_basename($report->reportable_type) }} from
                    {{ $report->reportable->created_at->diffForHumans() }} reported
                    {{ $report->created_at->diffForHumans() }}
                </p>

                <p class="card-subtitle mb-2 text-body-secondary">Reported by
                    @if ($report->reporter)
                        <x-profile-link :user="$report->reporter" />
                    @else
                        <strong>Anonymous</strong>
                    @endif
                    for <strong>{{ $report->reason_label }}</strong>.
                </p>

                @if ($report->details)
                    <p class="card-text text-muted">Reason</p>
                    <blockquote class="blockquote fs-6">
                        {{ $report->details }}
                    </blockquote>
                @endif

                @if ($postPreview)
                    <div x-data="{ open: true }">
                        <template x-if="open">
                            <div>
                                <x-ui.button-link @click="open = false" variant="outline-secondary" class="mb-2">
                                    Hide preview
                                </x-ui.button-link>
                                <x-post :post="$postPreview" :preview=true />
                            </div>
                        </template>
                        <template x-if="!open">
                            <x-ui.button-link @click="open = true" variant="outline-secondary">
                                Show preview
                            </x-ui.button-link>
                        </template>
                    </div>
                @else
                    <p class="text-muted"><em>No content preview available</em></p>
                @endif

                <x-ui.button-link href="{{ $report->reportable_link }}" target="_blank" variant="outline-secondary"
                    class="mt-2">
                    <x-slot name="icon">
                        <x-heroicon-s-arrow-top-right-on-square class="icon_mini" />
                    </x-slot>
                    View in context
                </x-ui.button-link>
            </div>
        </div>
    </div>

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

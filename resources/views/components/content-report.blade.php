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

                <blockquote class="blockquote">
                    {{ $report->details }}
                </blockquote>
            @endif

            @if ($postPreview)
                <div x-data="{ open: false }">
                    <template x-if="open">
                        <div>
                            <button type="button" @click="open = false" class="btn btn-sm btn-outline-secondary mt-2 mb-2">
                                Hide preview
                            </button>
                            <x-post :post="$postPreview" :preview=true />
                        </div>
                    </template>
                    <template x-if="!open">
                        <button type="button" @click="open = true" class="btn btn-sm btn-outline-secondary mt-2">
                            Show preview
                        </button>
                    </template>
                </div>
            @else
                <p class="text-muted"><em>No content preview available</em></p>
            @endif
            <a href="{{ $report->reportable_link }}" target="_blank"
                class="btn btn-sm btn-outline-secondary mt-2 d-inline-flex align-items-center gap-1">
                <x-heroicon-s-arrow-top-right-on-square class="icon_mini me-1" />
                View in context
            </a>
        </div>

        <div class="ms-md-3 text-end mt-3 mt-md-0">
            {{-- <a href="{{ route('admin.reports.show', $report->id) }}" class="btn btn-sm btn-primary"> --}}
            <a href="" class="btn btn-sm btn-primary">
                Review
            </a>
        </div>
    </div>
</div>

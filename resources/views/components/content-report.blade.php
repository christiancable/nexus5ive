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

        <div class="ms-md-3 text-end mt-3 mt-md-0">
            <x-ui.button-link href="#" variant="primary">
                Review
            </x-ui.button-link>

        </div>
    </div>
</div>

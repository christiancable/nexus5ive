<div class="card mb-3" id="{{ $id }}">
    @if ($title)
        <div class="card-header bg-primary text-white">
            <span class="card-title">{{ $title }}</span>
        </div>
    @endif

    <div class="card-body">
        <div class="d-flex justify-content-between">
            <div>
                <x-profile-link :url=$authorUrl :username=$authorName/> <span class="text-muted">&ndash;</span> {{$authorPopname}}
            </div>
            <div>
                <x-heroicon-s-flag class="icon_mini" />
            </div>
        </div>
        <small class="{{ $timeClass }}">{{ $formattedTime }}</small>
        <p class="card-text">
            <hr>
            {!! $content !!}
        </p>
        @if ($editedByInfo)
            <footer class="d-flex justify-content-end">
                <small class="text-muted">{!! $editedByInfo !!}</small>
            </footer>
        @endif
    </div>
</div>

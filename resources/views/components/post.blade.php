
<div class="card mb-3" id="{{ $id }}">
    @if ($title)
        <div class="card-header bg-primary text-white">
            <span class="card-title">{{$title}}</span>
        </div>
    @endif

    <div class="card-body">
        <div class="d-flex justify-content-between">
        <span>{!! $authorLink !!}</span>
        <small class="{{$timeClass}}">{{$formattedTime}}</small>
        </div>
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

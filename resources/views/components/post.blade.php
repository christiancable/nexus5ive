<div class="card mb-3" id="{{ $id }}">
    @if ($title)
        <div class="card-header bg-primary text-white">
            <span class="card-title">{{ $title }}</span>
        </div>
    @endif

    <div class="card-body">
        <div class="d-flex justify-content-between">
            @if ($anonymous)
                Unknown User
            @else 
            <div>
                <x-profile-link :user="$author"/> <span class="text-muted">&ndash;</span> {{$popname}}
            </div>
            @endif
            
            @if($preview != true)
            <div>
                <a href="{{ action('App\Http\Controllers\Nexus\PostController@report', ['post' => $id]) }}"><x-heroicon-s-flag class="icon_mini" /></a>
            </div>
            @endif
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

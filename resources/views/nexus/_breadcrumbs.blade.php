<div class="container mb-3">
    {{-- <nav aria-label="breadcrumb" class="border rounded bg-secondary-subtle opacity-75"> --}}
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb my-3">
            @foreach ($breadcrumbs as $crumb)
                @if ($crumb['route'])
                    <li class="breadcrumb-item"><a href="{{ $crumb['route'] }}">{{ $crumb['title'] }}</a></li>
                @else
                    <li class="breadcrumb-item active" aria-current="page">{{ $crumb['title'] }}</li>
                @endif
            @endforeach
        </ol>
    </nav>
</div>

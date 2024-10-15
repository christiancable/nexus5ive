<div class="card my-3">
  <div class="card-body">
    <h5 class="card-title">{{ $topic->title }}</h5>
    <p class="card-text">
        {!! App\Helpers\NxCodeHelper::nxDecode($topic->intro) !!}
    </p>
     
    <p class="card-text small text-muted">Removed: {{ $topic->deleted_at->diffForHumans() }}</p>

    @if ($destinationSections->count() != 0)
    <form action="{{ route('archive.topic', $topic->id) }}" method="POST" class="form">
        @csrf
        <div class="form-row align-items-center justify-content-end">
            <div class="col-auto my-1">
                <label for="destination">Restore topic to</label>
            </div>
            <div class="col-auto my-1">
                <select name="destination" class="custom-select mr-sm-2">
                    @foreach($destinationSections as $destSection)
                        <option value="{{ $destSection->id }}">{{ $destSection->title }}</option>
                    @endforeach
                </select>
            </div>
            
            <div class="col-auto my-1">
                <button type="submit" class="btn btn-primary">Restore Topic</button>
            </div>
        </div>
    </form>
    @endif
  </div>
</div>

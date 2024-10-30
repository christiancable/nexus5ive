<?php
    $topics = $section->trashedTopics->pluck('title')->toArray();
?>
<div class="card my-3">
  <div class="card-body">
    <h5 class="card-title">{{ $section->title }}</h5>
    <h6 class="card-subtitle mb-2 text-muted">Moderated by {{ $section->moderator->username }}</h6>
    
    @if(count($topics) != 0) 
        <p class="card-text"><strong>Topics &ndash;</strong> <small>{!! implode(', ', $topics) !!}</small></p>
    @endif
    
    <p class="card-text small text-muted">Removed: {{ $section->deleted_at->diffForHumans() }}</p>

    @if ($destinationSections->count() != 0)
    <form action="{{ route('archive.section', $section->id) }}" method="POST" class="form">
        @csrf
        <div class="form-row align-items-center justify-content-end">
            <div class="col-auto my-1">
                <label for="destination">Restore section to</label>
            </div>
            <div class="col-auto my-1">
                <select name="destination" class="custom-select mr-sm-2">
                    @foreach($destinationSections as $destSection)
                        <option value="{{ $destSection->id }}">{{ $destSection->title }}</option>
                    @endforeach
                </select>
            </div>
            
            <div class="col-auto my-1">
                <button type="submit" class="btn btn-primary">Restore Section</button>
            </div>
        </div>
    </form>
    @endif
  </div>
</div>

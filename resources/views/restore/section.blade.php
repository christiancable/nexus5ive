<?php
    $topics = $section->trashedTopics->pluck('title')->toArray();
?>
<div class="card my-3">
  <div class="card-body">
    <h5 class="card-title">{{$section->title}}</h5>
    <h6 class="card-subtitle mb-2 text-muted">Moderated by {{ $section->moderator->username}}</h6>
    {{-- <p class="card-text">Some quick example text to build on the card title and make up the bulk of the card's content.</p> --}}
        @if(count($topics) != 0 ) 
            <p class="card-text"><strong>Topics &ndash;</strong> <small>{!! implode(', ', $topics) !!}</small></p>
        @endif
    
    <p class="card-text small text-muted">Removed: {{$section->deleted_at->diffForHumans()}}</p>

    @if ($destinationSections->count() != 0)
    {!! Form::open(
                array(
                    'route'     => ['archive.section', $section->id],
                    'class'     => 'form',
                    )
    ) !!}
        <div class="form-row align-items-center justify-content-end">
            <div class="col-auto my-1">
                <label for="inlineFormCustomSelect">Restore section to</label>
            </div>
            <div class="col-auto my-1">
            {!! 
                Form::select("destination",
                    $destinationSections->pluck('title','id')->toArray(),
                    null,
                    ['class' => 'custom-select mr-sm-2']
                )
            !!}
            </div>
            
            <div class="col-auto my-1">
            <button type="submit" class="btn btn-primary">Restore Section</button>
            </div>
        </div>
    </form>
    @endif
  </div>
</div>

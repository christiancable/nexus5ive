<div class="card my-3">
  <div class="card-body">
    <h5 class="card-title">{{$topic->title}}</h5>
    <p class="card-text">
    {!! App\Helpers\NxCodeHelper::nxDecode($topic->intro) !!}
    </p>
     
    <p class="card-text small text-muted">Removed: {{$topic->deleted_at->diffForHumans()}}</p>

    @if ($destinationSections->count() != 0)
    {!! Form::open(
                array(
                    'route'     => ['archive.topic', $topic->id],
                    'class'     => 'form',
                    )
    ) !!}
        <div class="form-row align-items-center justify-content-end">
            <div class="col-auto my-1">
                <label for="inlineFormCustomSelect">Restore topic to</label>
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
            <button type="submit" class="btn btn-primary">Restore Topic</button>
            </div>
        </div>
    </form>
    @endif
  </div>
</div>

<div class="container">

    <div class="row">

        <div class="col-sm-12">
            <h3>{{$topic->title}}</h3>
            <p>{{$topic->intro}}</p>
            <p class="small text-muted">Removed: {{$topic->deleted_at->diffForHumans()}}</p>
        </div>

    </div>
    
    @if ($destinationSections->count() != 0)
        <div class="row">
            <div class="col-sm-12">
                {!! Form::open(
                    array(
                        'route'     => ['archive.topic', $topic->id],
                        'class'     => 'form',
                        )
                ) !!}

                <div class="row">

                    <div class="col-xs-12 col-sm-8">
                        <label> Restore to  {!! 
                            Form::select("destination",
                                $destinationSections->pluck('title','id')->toArray(),
                                ['class' => 'form-control'])
                                !!}
                        </label>
                    </div>

                    <div class=" col-xs-12 col-sm-4">
                        {!!                  
                            Form::button("<span class='glyphicon glyphicon-open'></span>&nbsp;&nbsp;Restore Topic",
                                array(
                                    'type'  => 'submit',
                                    'class' => "btn btn-primary col-xs-12 ", 
                                    )
                                )
                        !!}
                    </div>
                </div>
                {!! Form::close() !!}
            </div>
        </div>
    @endif
    <hr>
</div>
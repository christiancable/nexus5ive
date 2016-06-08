<div class="row">

    <div class="col-sm-12">
        <h3>{{$section->title}}</h3>
        <p class="small pull-right text-muted hidden-xs">Moderated by {{ $section->moderator->username}}</p>
        <p>{{$section->intro}}</p>
        <?php
        $topics = $section->trashedTopics->pluck('title')->toArray();
        ?>
        @if(count($topics) != 0 ) 
            <p><strong>Topics &ndash;</strong> <small>{!! implode(', ', $topics) !!}</small></p>
        @endif
        <p class="small text-muted">Removed: {{$section->deleted_at->diffForHumans()}}</p>
    </div>

</div>

@if ($destinationSections->count() != 0)
    <div class="row">
        <div class="col-sm-12">
            {!! Form::open(
                array(
                    'route'     => ['archive.section', $section->id],
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
                        Form::button("<span class='glyphicon glyphicon-open'></span>&nbsp;&nbsp;Restore Section",
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

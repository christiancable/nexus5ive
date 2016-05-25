<div class="container">
    <div class="row">     
        <div class="col-sm-6">
            <p><strong>{{$section->title}}</strong></p>
            <p class="small">Moderated by {{ $section->moderator->username}}</p>
            <p>{{$section->intro}}</p>
            <?php
                $topics = $section->trashedTopics->pluck('title')->toArray();
            ?>

            @if(count($topics) != 0 ) 
                <p><strong>Contains</strong> <small>{!! implode(', ', $topics) !!}</small></p>
            @endif

            <p class="small text-muted">Removed: {{$section->deleted_at->diffForHumans()}}</p>
        </div>
        @if ($destinationSections->count() != 0)
            <div class="col-sm-6">

            {!! Form::open(
            array(
                'route'     => ['restore.section', $section->id],
                'class'     => 'form',
                )
            ) !!}
                <label> Restore to  {!! 
                                        Form::select("destination",
                                        $destinationSections->pluck('title','id')->toArray(),
                                        ['class' => 'form-control'])
                                    !!}
                </label>
                {!! 
                    Form::button("<span class='glyphicon glyphicon-open'></span>&nbsp;&nbsp;Restore Section",
                        array(
                            'type'  => 'submit',
                            'class' => "btn pull-right btn-info", 
                            )
                    )
                !!}
                {!! Form::close() !!}
            </div>
        @endif
    </div>
</div>
<hr/>
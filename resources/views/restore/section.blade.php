<div class="container">
    <div class="row">     
        <div class="col-sm-6">
            <p><strong>{{$section->title}}</strong></p>
            <p>{{$section->intro}}</p>
            <?php
                $topics = $section->trashedTopics->pluck('title')->toArray();
            ?>
            @if(count($topics) != 0 ) 
                <strong>Contains</strong> <small>{!! implode(', ', $topics) !!}</small>
            @endif

            <p class="small text-muted">Removed: {{$section->deleted_at->diffForHumans()}}</p>
        </div>
        <div class="col-sm-6">

        {!! Form::open(
        array(
            // 'route'     => ['restore.section', $post->id],
            'route'     => ['restore.section', $section->id],
            'class'     => 'form',
            // 'method'    => 'PATCH',
            // 'name'      => 'Section'.$section->id,
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
    </div>
</div>
<hr/>
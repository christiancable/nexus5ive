<div class="container">
    <div class="row">     
        <div class="col-sm-6">
            <p><strong>{{$section->title}}</strong></p>
            <p>{{$section->intro}}</p>
            <p class="small text-muted">Removed: {{$section->deleted_at->diffForHumans()}}</p>
        </div>
        <div class="col-sm-6">
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
        </div>
    </div>
</div>
<hr/>
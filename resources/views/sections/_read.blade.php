<div class="col-md-4">
    <div class="panel panel-primary">
        <div class="panel-heading">
            <h2 class="panel-title"><a href="{{ action('Nexus\SectionController@show', ['id' => $subSection->id])}}">{{$subSection->title}}</a></h2>
        </div>

        <div class="panel-body">
            <p><em>{{$subSection->intro}}</em></p>
            {{--  <p><a class="btn btn-default" href="{{ action('Nexus\SectionController@show', ['id' => $subSection->id])}}" role="button">View details &raquo;</a></p> --}}
        </div>
    </div>
</div>
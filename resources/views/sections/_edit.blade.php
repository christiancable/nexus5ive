{{-- this is for moderators to edit sub sections --}}



    <div class="panel panel-primary">
        


        <div class="panel-heading">
            <h2 class="panel-title"><a href="{{ action('Nexus\SectionController@show', ['id' => $subSection->id])}}">{{$subSection->title}}</a></h2>
        </div>

        <div class="panel-body">

            <p><em>{{$subSection->intro}}</em></p>
            {{--  <p><a class="btn btn-default" href="{{ action('Nexus\SectionController@show', ['id' => $subSection->id])}}" role="button">View details &raquo;</a></p> --}}
            <p>You own the current section</p>
            <ul>
            <li><strong>Title</strong> {{$subSection->title}}</li>
            <li><strong>Introduction</strong> {{$subSection->intro}}</li>
            <li><strong>Moderator</strong> {{$subSection->moderator->username}}</li>
            <li><strong>Order</strong> {{$subSection->weight}}</li>
            </ul>
        </div>
    </div>

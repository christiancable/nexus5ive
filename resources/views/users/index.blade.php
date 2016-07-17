@extends('layouts.master')

@section('breadcrumbs')
@include('_breadcrumbs', $breadcrumbs)
@endsection 

@section('meta')
<title>View Users</title>
@endsection

@section('content')
        <div class="container">
            <div class="content">

{{-- <div id="the-basics">
  <input class="typeahead form-control" type="text" placeholder="States of USA">
  <input type="hidden" name="_token" value="{{ csrf_token() }}">
</div> --}}



                <h1 class="title">Users</h1>


{!! Form::open(['url' => 'search', 'id'=>'usersearch']) !!}
    <div class="form-group" role="search">
        {!! Form::text('text', null, ['class'=> 'typeahead form-control', 'placeholder'=>"Username"]) !!}
    </div>

    <div class="row">    
    <div class="col-sm-12">
        <div class="form-group">          
            {!! Form::button("<span class='glyphicon glyphicon-search'></span>&nbsp;&nbsp;Search",
                array(
                    'type'  => 'submit',
                    'class' => "btn pull-right btn-primary col-xs-12 col-sm-3"
                    )
            ) !!}
        </div>
    </div>
</div>
{!! Form::close() !!}


<hr/>



                <?php
                    $skiplinks = array();
                    $currentLetter = "";
                    $previousLetter = "";
                    $allLetters = array();
                    $listItems = '';

                    $listItems .= "<div class='row'>";
                ?>
    
                @foreach ($users as $user) 
                        <?php
                        $currentLetter = strtoupper($user->username)[0];

                        if ($currentLetter != $previousLetter) {
                            if ($previousLetter !="") {
                                $listItems .= '</ul></div>';
                                $listItems .= '</div>';
                            }
                            
                            $allLetters[] = $currentLetter;
                            $previousLetter = $currentLetter;

                           // start a new row of panels
                            if (!((count($allLetters)-1) % 3)) {
                                 $listItems .= "</div>";
                                 $listItems .= "<div class='row'>";
                            }

                            $listItems .= "<div class='col-md-4'>";
                            $listItems .= '<div class="panel panel-default">';
                            $listItems .= "<div id='$currentLetter' class='panel-heading'>$currentLetter</div>";
                            $listItems .= '<ul class="list-group">';
                        } else {
                        }
                        $url =  action('Nexus\UserController@show', ['user_name' => $user->username]);
                        $listItems .= '<li class="list-group-item"><a href="'. $url . '">' . $user->username . '</a></li>';
                        ?>
                @endforeach

                @if (count($allLetters))                
                <ul class="nav nav-pills">
                    @foreach ($allLetters as $letter)
                        <li><a href="#{{$letter}}">{{$letter}}</a></li>
                    @endforeach
                </ul>
                <hr>
                @endif


                {!! $listItems !!}
                </ul>
            </div>
        </div>
@endsection


@section('javascript')
<script src="https://cdnjs.cloudflare.com/ajax/libs/typeahead.js/0.11.1/typeahead.bundle.min.js"></script>  

<script type="text/javascript">

$('#usersearch .typeahead').typeahead({
  hint: true,
  highlight: true,
  minLength: 2
},
{
   name: 'users',
  source:  function (query, process, process) {
        return $.post('{{route('api.users')}}', { query: query, _token: '{!! csrf_token() !!}' }, function (data) {
                return process(data);
            });
    },
  templates: {
    empty: [
        '<div class="list-group search-results-dropdown"><div class="list-group-item">Nothing found.</div></div>'
    ],
    header: [
        '<div class="list-group search-results-dropdown">'
    ],
    suggestion: function (data) {
         return '<a href="{!! Request::url() !!}/' + data.username + '" class="list-group-item">' + data.username + '</a>'
    }

    }
});
</script>
@endsection
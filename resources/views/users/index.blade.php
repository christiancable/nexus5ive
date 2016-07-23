@extends('layouts.master')

@section('breadcrumbs')
@include('_breadcrumbs', $breadcrumbs)
@endsection 

@section('meta')
<title>View Users</title>
@endsection

@section('content')
        

<div class="container">
    <h1>Users</h1>
    <span class="lead">"I fight for the Users"</span>
</div>
<hr>

<div class="container">
{!! Form::open(['url' => 'search', 'id'=>'usersearch']) !!}
    <div class="form-group" role="search">
        <select autofocus id="selectUser" class="form-control">
            @foreach ($users as $user) 
            <option value="/{{Request::path()}}/{{$user->username}}">{{$user->username}}</option>
            @endforeach
            <option value="" selected="selected" disabled="disabled">Select User</option>
        </select>
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
<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/css/select2.min.css" rel="stylesheet" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/js/select2.min.js"></script>



<script type="text/javascript">
$("#selectUser").select2();

$("#selectUser").change(function() {
    window.location.replace($(this).val());
});
</script>
@endsection
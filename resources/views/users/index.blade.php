@extends('layouts.master')

@section('meta')
<title>View Users</title>
@endsection

@section('content')
        <div class="container">
            <div class="content">
                <h1 class="title">Users</h1>
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
                        $currentLetter = strtoupper($user->user_name)[0];

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
                        $url =  action('Nexus\UserController@show', ['user_name' => $user->user_name]);
                        $listItems .= '<li class="list-group-item"><a href="'. $url . '">' . $user->user_name . '</a></li>';
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

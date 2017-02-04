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

<div class="container" id="users-list">
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
$previousLetter = '';
$currentLetter = '';
?>
                @foreach ($users as $user) 
                <?php
                $currentLetter = strtoupper($user->username[0]);
                ?>
                @if ($currentLetter !== $previousLetter) 
                    <h2 class="bg-info"><span>{{ $currentLetter }}</span></h2>
                    <hr/>
                @endif 
                     @include('users._panel', $user)
                <?php
                $previousLetter = $currentLetter;
                ?>
                @endforeach
              
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
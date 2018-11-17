@extends('layouts.master')

@section('breadcrumbs')
@include('_breadcrumbs', $breadcrumbs)
@endsection 

@section('meta')
<title>View Users</title>
@endsection

@section('content')
        

<div class="container">
    @include('_heading', [$heading = 'Users', $lead = '"I fight for the Users"'])    
</div>

<div class="container" id="users-list">

<div id="app" v-cloak>
<userlist :users="{{json_encode($users)}}"></userlist>
</div>

<div class="replace-with-vue">
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
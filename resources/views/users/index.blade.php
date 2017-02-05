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

@include('users.index_vue')

<div class="hidden-from-vue">
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
@include('users._panel_vue', $users)
@endsection
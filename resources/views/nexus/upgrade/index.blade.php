@extends('layouts.master')

@section('meta')
<title>View Users</title>
@endsection

@section('content')
        <div class="container">
            <div class="content">
                <h1 class="title">Users</h1>
                <ul>
    			
                @foreach ($classicUsers as $user) 
                        <li>{{$user->user_name}}</li>
                @endforeach
				</ul>
            </div>
        </div>
@endsection

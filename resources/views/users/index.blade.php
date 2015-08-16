@extends('layouts.master')

@section('meta')
<title>View Users</title>
@endsection

@section('content')
        <div class="container">
            <div class="content">
                <div class="title">Users</div>
                <ul>
                @foreach ($users as $user)
                    <li><a href="{{ url("/users/{$user->user_name}") }}">{{$user->user_name}}</a></li>               
                @endforeach
                </ul>
            </div>
        </div>
@endsection

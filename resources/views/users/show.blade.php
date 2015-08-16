@extends('layouts.master')

@section('meta')
<title>{{$user->user_name}}</title>
@endsection

@section('content')
        <div class="container">
            <div class="content">
                <h1>{{$user->user_name}}</h1>
                <h2>User Information</h2>
                <dl>        
                    <dt>Name</dt><dd>{{$user->user_realname}}</dd>

                    @if ($user->user_hideemail === 'no')
                        <dt>Email</dt><dd>{{$user->user_email}}</dd>
                    @else
                        <dt>Email</dt><dd>Hidden</dd>
                    @endif

                    <dt>Popname</dt><dd>{{$user->user_popname}}</dd>
                    <dt>Age</dt><dd>{{$user->user_age}}</dd>
                    <dt>Sex</dt><dd>{{$user->user_sex}}</dd>
                    <dt>Location</dt><dd>{{$user->user_town}}</dd>
                    
                    <dt>Further Information</dt><dd>{{$user->user_comment}}</dd>

                    <dt>Total Post</dt><dd>{{$user->user_totaledits}}</dd>
                    <dt>Total Visits</dt><dd>{{$user->user_totalvisits}}</dd>

                    <dt>Favourite Film</dt><dd>{{$user->user_film}}</dd>
                    <dt>Favourite Band</dt><dd>{{$user->user_band}}</dd>

                </dl>

                @if (count($user->sections))
                    <h2>Moderates</h2>
                    <ul>
                    @foreach ($user->sections as $section)
                        <li>{{$section->section_title}}</li>
                    @endforeach
                    </ul>
                @endif

                @if (count($user->comments))
                    <h2>Comments</h2>
                    <ul>
                    @foreach ($user->comments as $comment)
                        <li><strong><a href="{{ url("/users/{$comment->author->user_name}") }}">{{$comment->author->user_name}}</a></strong> - {{$comment->text}}</li>
                    @endforeach
                @endif
                </ul>
            </div>
        </div>
@endsection

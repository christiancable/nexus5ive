@extends('layouts.master')

@section('meta')
<title>{{$user->user_name}}</title>
@endsection

@section('content')
        <div class="container">
            <div class="content">
                <h1>{{$user->user_name}}</h1>
                <hr>

                
             {{--    <h2>User Information</h2> --}}

                <div class="row">
                <dl class="dl-horizontal col-md-6">        
                    <dt>Name</dt><dd>{{$user->user_realname}}</dd>

                    @if ($user->user_hideemail === 'no')
                        <dt>Email</dt><dd><a href="mailto:{{$user->user_email}}">{{$user->user_email}}</a></dd>
                    @else
                        <dt>Email</dt><dd><em>Hidden</em></dd>
                    @endif

                    <dt>Popname</dt><dd>{{$user->user_popname}}</dd>
                    <dt>Age</dt><dd>{{$user->user_age}}</dd>
                    <dt>Sex</dt><dd>{{$user->user_sex}}</dd>
                </dl>

                <dl class="dl-horizontal col-md-6">        
                    <dt>Location</dt><dd>{{$user->user_town}}</dd>
                    

                    <dt>Total Post</dt><dd>{{$user->user_totaledits}}</dd>
                    <dt>Total Visits</dt><dd>{{$user->user_totalvisits}}</dd>

                    <dt>Favourite Film</dt><dd>{{$user->user_film}}</dd>
                    <dt>Favourite Band</dt><dd>{{$user->user_band}}</dd>
                    <dt>Last Seen</dt><dd>{{$user->lastSeen}}</dd>

                </dl>
                </div>

                <div class="well">{!! nl2br($user->user_comment) !!}</div>
                @if (count($user->sections))
     {{--                <h2>Sections</h2> --}}
                    <span>If you like <strong>{{$user->user_name}}</strong> then check out these sections they moderate </span>
                    <!-- Single button -->
                    <div class="btn-group">
                      <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        Choose Section <span class="caret"></span>
                      </button>
                      <ul class="dropdown-menu">
                        @foreach ($user->sections as $section)
                            <li><a href="{{ url("/{$section->section_id}") }}">{{$section->section_title}}</a></li>
                        @endforeach
                      </ul>
                    </div>

                   <hr> 
                @endif

                <h2>Comments</h2>
                @include('comments.create', $user)
                @if (count($user->comments))
                    <ul>
                    @foreach ($user->comments as $comment)
                        <li><strong><a href="{{ url("/users/{$comment->author->user_name}") }}">{{$comment->author->user_name}}</a></strong> - {{$comment->text}}</li>
                    @endforeach
                @endif
                </ul>
            </div>
        </div>
@endsection

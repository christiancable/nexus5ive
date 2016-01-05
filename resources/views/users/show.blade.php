@extends('layouts.master')

@section('meta')
<title>{{$user->username}}</title>
@endsection

@section('content')
        <div class="container">
            <div class="content">
                <h1>{{$user->username}}</h1>
                <hr>

                @if (Auth::user()->id == $user->id)
                    @include('users._edit', $user)
                @else
                    @include('users._read', $user)
                @endif

                <h2>Comments</h2>
                @include('comments.create', $user)
                @if (count($user->comments))
                    <table class="table table-striped table-condensed">
                    <tbody>

                    @if (Auth::user()->id == $user->id)
                        @foreach ($user->comments as $comment)
                            @include('comments._edit', $comment)
                        @endforeach
                    @else
                        @foreach ($user->comments as $comment)
                            @include('comments._read', $comment)
                        @endforeach
                    @endif
                    </tbody>
                    </table>
                @endif
            </div>
        </div>
@endsection

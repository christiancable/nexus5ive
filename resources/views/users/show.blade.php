@extends('layouts.master')

@section('breadcrumbs')
@include('_breadcrumbs', $breadcrumbs)
@endsection 

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

                <h2 id="comments">Comments</h2>
                @include('comments.create', $user)
                @if (count($comments))
                    <table class="table table-striped table-condensed">
                    <tbody>           
                        @foreach ($comments as $comment)
                            @if (Auth::user()->id == $user->id)
                                @include('comments._edit', $comment)
                            @else 
                                @include('comments._read', $comment)
                            @endif 
                        @endforeach
                    </tbody>
                    </table>
                    @if (Auth::user()->id == $user->id)
                        @include('comments._clear', $user)
                    @endif
                    {{ $comments->links() }}
                @endif
            </div>
        </div>
@endsection

@extends('layouts.master')

@section('breadcrumbs')
@include('_breadcrumbs', $breadcrumbs)
@endsection 

@section('meta')
<title>{{$user->username}}</title>
@endsection


@section('content')
    <div class="container">

        @if (Auth::user()->id == $user->id)
            @include('shared._heading', [
                $heading = $user->username,
            ])
            @include('users._edit', $user)
        @else
            @include('shared._heading', [
                $heading = $user->username,
                $lead = $user->name,
                $introduction = $user->popname,
                $tag = 'Inactive',
            ])
            @include('users._read', $user)
        @endif

        <h2 id="comments">Comments</h2>

        @include('comments.create', $user)

        @if (count($comments))
            <table class="table table-striped">
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
@endsection

@extends('nexus.layouts.master')

@section('breadcrumbs')
@include('nexus._breadcrumbs', $breadcrumbs)
@endsection 

@section('meta')
<title>{{$user->username}}</title>
@endsection

@section('content')
    <div class="container">

        @if (Auth::user()->id == $user->id)
            @include('nexus.shared._heading', [
                $heading = $user->username
            ])
            @include('nexus.users._edit', $user)
        @else
            @include('nexus.shared._heading', [
                $heading = $user->username,
                $lead = $user->name,
                $introduction = $user->popname
            ])
            @include('nexus.users._read', $user)
        @endif

        <h2 id="comments">Comments</h2>

        @include('nexus.comments.create', $user)

        @if (count($comments))
            <table class="table table-striped">
                <tbody>           
                @foreach ($comments as $comment)
                    @if (Auth::user()->id == $user->id)
                        @include('nexus.comments._edit', $comment)
                    @else 
                        @include('nexus.comments._read', $comment)
                    @endif 
                @endforeach
                </tbody>
            </table>
            @if (Auth::user()->id == $user->id)
                @include('nexus.comments._clear', $user)
            @endif
            {{ $comments->links() }}
        @endif
        
    </div>
@endsection

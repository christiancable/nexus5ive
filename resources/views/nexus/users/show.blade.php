@extends('nexus.layouts.master')

@section('breadcrumbs')
    @include('nexus._breadcrumbs', $breadcrumbs)
@endsection

@section('meta')
    <title>{{ $user->username }}</title>
@endsection

@section('content')
    <div class="container">

        @if (Auth::user()->id == $user->id && Auth::user()->can('update', $user))
            <x-heading heading="{{ $user->username }}" />
            @include('nexus.users._edit', $user)
        @else
            <x-heading heading="{{ $user->username }}" lead="{{ $user->name }}" introduction="{{ $user->popname }}" />
            @include('nexus.users._read', $user)
        @endif

        <h2 id="comments">Comments</h2>

        @can('create', App\Models\Comment::class)
            @include('nexus.comments.create', $user)
        @endcan

        @if (count($comments))
            <table class="table table-striped user-comments">
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
            @if (Auth::user()->id == $user->id && Auth::user()->can('update', $user))
                @include('nexus.comments._clear', $user)
            @endif
            {{ $comments->links() }}
        @endif

    </div>
@endsection

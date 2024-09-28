@extends('nexus.layouts.master')

@section('meta')
    <title>Latest Posts</title>
@endsection

@section('breadcrumbs')
    @include('nexus._breadcrumbs', $breadcrumbs)
@endsection

@section('content')
    <div class="container">
        <x-heading heading="Latest Posts" lead="The freshest posts from across {{ config('nexus.name') }}">
            <x-slot:icon>
                <x-heroicon-s-bolt class="icon_large mr-1" aria-hidden="true" />
            </x-slot>
        </x-heading>

        @if (count($topics))
            @foreach ($topics as $topic)
                @include('nexus.topics._latest', $topic)
            @endforeach
        @endif
    </div>
@endsection

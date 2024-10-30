@extends('nexus.layouts.master')

@section('breadcrumbs')
    @include('nexus._breadcrumbs', $breadcrumbs)
@endsection

@section('meta')
    <title>View Users</title>
@endsection

@section('content')
    <div class="container">
        <x-heading heading="Users" lead='"I fight for the Users"'>
            <x-slot:icon>
                <x-heroicon-s-users class="icon_large mr-1" aria-hidden="true" />
            </x-slot>
        </x-heading>
    </div>

    <div class="container" id="users-list">
        @livewire('userlist')
    </div>


@endsection

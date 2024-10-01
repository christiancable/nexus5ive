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
        @livewire('userlist', ['users' => $users])

        <div class="replace-with-vue card-deck">

            @foreach ($users as $user)
                @include('nexus.users._panel', $user)

                {{-- non-moderators get a card desk layout --}}
                {{-- wrap sub-sections: 1 col for sm, 2 for md, 3 for lg --}}
                <div class="w-100 d-sm-block d-md-none"></div>

                @if ($loop->iteration % 2 === 0)
                    <div class="w-100 d-none d-md-block d-lg-none"></div>
                @endif

                @if ($loop->iteration % 3 === 0)
                    <div class="w-100 d-none d-lg-block"></div>
                @endif
            @endforeach

        </div>

    </div>
@endsection

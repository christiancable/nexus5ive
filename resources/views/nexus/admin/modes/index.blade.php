@extends('nexus.layouts.master')

@section('meta')
    <title>Theme</title>
@endsection

@section('breadcrumbs')
    @include('nexus._breadcrumbs', $breadcrumbs)
@endsection

@section('content')
    <div class="container">
        <x-heading heading="Settings" lead="Change theme for the entire BBS">
            <x-slot:icon>
                <x-heroicon-s-wrench class="icon_large mr-1" aria-hidden="true" />
            </x-slot>
        </x-heading>
    </div>
    @livewire('settings')
@endsection

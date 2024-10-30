@extends('nexus.layouts.master')

@section('meta')
    <title>Chat</title>
@endsection

@section('breadcrumbs')
    @include('nexus._breadcrumbs', $breadcrumbs)
@endsection 

@section('content')
    <div class="container">
        <x-heading heading="Chat" lead="Modem Talking">
            <x-slot:icon>
                <x-heroicon-s-chat-bubble-left-right class="icon_large mr-1" aria-hidden="true" />
            </x-slot>
        </x-heading>
    </div>

    <livewire:chat :selectedUser="$selectedUser"/>
@endsection
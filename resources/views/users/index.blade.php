@extends('layouts.master')

@section('breadcrumbs')
@include('_breadcrumbs', $breadcrumbs)
@endsection 

@section('meta')
<title>View Users</title>
@endsection

@section('content')

<div class="container">
    @include('shared._heading', [$heading = 'Users', $lead = '"I fight for the Users"', $icon = 'people'])    
</div>

<div class="container" id="users-list">

    <div id="app" v-cloak>
        <userlist :users="{{json_encode($users)}}"></userlist>
    </div>

    <div class="replace-with-vue card-deck v-cloak">

        @foreach ($users as $user)        

            @include('users._panel', $user)

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
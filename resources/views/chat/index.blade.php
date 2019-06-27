@extends('layouts.master')

@section('breadcrumbs')
@include('_breadcrumbs', $breadcrumbs)
@endsection 

@section('meta')
<title>Chit Chat</title>
@endsection

@section('content')

<div class="container">
    @include('shared._heading', [$heading = 'Ugh', $lead = 'Ugh Ugh', $icon = 'people'])    
</div>



<div class="container" id="users-list">


        <div class="d-flex flex-column">
            
        @foreach ($conversation as $message)        

            @if (Auth::user()->id === $message->author->id)
                @php $mine = true @endphp
            @else 
                @php $mine = false @endphp
            @endif 

            

            {{-- <div class="{{ $mine ? 'align-self-end' : 'align-self-start'}}"> --}}
            <div>
            
            @if ($loop->first)
                @include('chat._name', ['username' => $message->author->username])
                
                @php 
                    $previousMessageAuthorId = $message->author->id;
                @endphp       
            @else
                @if ($previousMessageAuthorId != $message->author->id)
                    @include('chat._name', ['username' => $message->author->username, $mine])
                @endif 
                @php 
                    $previousMessageAuthorId = $message->author->id;
                @endphp       
            @endif


                @include('chat._message', [$message, $mine])
            </div>



        @endforeach
        
        </div>
    
</div>
@endsection
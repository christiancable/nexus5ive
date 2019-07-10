@extends('layouts.master')

@section('breadcrumbs')
@include('_breadcrumbs', $breadcrumbs)
@endsection 

@section('meta')
<title>Chit Chat</title>
@endsection

@section('content')

<style>
    
    .main-container {
        height: 75vh; /* or position:absolute; height:100%; */
    }
    
    .fixed-container {
        height: 12rem;
        padding: 1rem;
        background-color: #0B5AB0;
        color: white;
        /* overflow: hidden; */
    }
    
    .overflow-container {
        flex: 1;
        overflow: auto;
    }
    
    .overflow-content {
        height: 2000px;
        color: black;
        background-color: #ddd;
        padding: 20px;
    }
    
    .content-wrapper {
        display: flex;
        flex: 1;
        min-height: 0px; /* IMPORTANT: you need this for non-chrome browsers */
    }
    
</style>


<div class="container">
    @include('shared._heading', [$heading = 'Chat', $lead = 'Modem Talking', $icon = 'people'])    
</div>

<div class="container">
    <div class="row">
        <div class="col-md-3 d-md-block">
            
            <nav class="list-group" id="chat-partners">
                @foreach ($conversationPartners as $partner)
                <a class="list-group-item list-group-item-action {{ $partner === $currentPartner ? 'active' : ''}}" 
                href="/chat/{{ $partner }}">
                {{ $partner }}
            </a>
            @endforeach
        </nav>
    </div>
    
    <div class="col-md-9 d-flex flex-column main-container">   
        <div class="content-wrapper">
            <div class="overflow-container">
                <div class="overflow-content">
                    @include('chat._conversation', [$conversation])
                    </div> 
                </div>
            </div>
            
            @if ($currentPartner)
            <div class="fixed-container">
                @include('chat._messageform')
            </div>
            @endif
        </div>
    </div>
</div>

<script>
    // document.getElementById("chat-conversation").scrollTop = 100000000000;
    // document.getElementsByClassName("overflow-container")[0].scrollTop = 100000000000;
</script> 
@endsection
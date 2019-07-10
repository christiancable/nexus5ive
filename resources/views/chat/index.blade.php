@extends('layouts.master')

@section('breadcrumbs')
@include('_breadcrumbs', $breadcrumbs)
@endsection 

@section('meta')
<title>Chit Chat</title>
@endsection

@section('content')

<div class="container">
    @include('shared._heading', [$heading = 'Chat', $lead = 'Modem Talking', $icon = 'people'])    
</div>

<div class="container">
    <div class="row">
        <div class="col-md-3 d-md-block">
        @include('chat._chatlist', [$conversationPartners, $currentPartner])
    </div>
    
    <div class="col-md-9 d-flex flex-column chat-container">   
        @if ($currentPartner)
        <div class="chat-wrapper">
            <div class="chat-content" id="chat-content">
                <div class="chat-conversation d-flex flex-column  justify-content-end">
                    @include('chat._conversation', [$conversation])
                    </div> 
                </div>
            </div>
            
            <div class="chat-post">
                @include('chat._messageform')
            </div>
        </div>
    </div>
    @else
    <div class="content">
        <p>No chat selected</p>
    </div>
    @endif
</div>

<script>
    // scroll the chat window down
    document.getElementById("chat-content").scrollTop = 100000000000;
</script> 
@endsection
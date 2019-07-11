@extends('layouts.master')

@section('breadcrumbs')
@include('_breadcrumbs', $breadcrumbs)
@endsection 

@section('meta')
<title>Messages</title>
@endsection

@section('content')

<div class="container">
    @include('shared._heading', [$heading = 'Messages', $lead = 'Modem Talking', $icon = 'chat'])    
</div>

<div class="container">
    <div class="row">
        <div class="col-md-3 d-md-block">
        @include('chat._chatlist', [$conversationPartners, $currentPartner])
    </div>
    
    <div class="col-md-9 d-flex flex-column chat-container">   
    @if ($currentPartner)
        <div class="chat-wrapper">
            <div class="chat-content d-flex flex-column  justify-content-end" id="chat-content">
            @if (count($conversation) > 0)
                <div class="chat-conversation">
                    @include('chat._conversation', [$conversation])
                </div> 
            @else
                @include('chat._new_conversation', [$currentPartner])
            @endif
            </div>
        </div>
        <div class="chat-post">
            @include('chat._messageform')
        </div>
        </div>
    </div>
    @else
    <div class="content">
        @include('chat._create_chat')
    </div>
    @endif
</div>

<script>
    // scroll the chat window down
    document.getElementById("chat-content").scrollTop = 100000000000;
</script> 
@endsection
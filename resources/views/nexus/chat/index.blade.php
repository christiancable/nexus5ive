@extends('layouts.master')

@section('breadcrumbs')
@include('nexus._breadcrumbs', $breadcrumbs)
@endsection 

@section('meta')
<title>Messages</title>
@endsection

@section('content')

<div class="container">
    @include('shared._heading', [$heading = 'Messages', $lead = 'Modem Talking', $icon = 'chat'])    
</div>

<div class="container" id="app">
    <div class="row">
        <div class="col-lg-3 d-none d-lg-block">
            <chat-list current-chat="{{$currentPartner}}"></chat-list>
        </div>
    
        <div class="col-lg-9 d-flex flex-column chat-container">   
        <chat-conversation current-chat="{{$currentPartner}}" username="{!! Auth::user()->username !!}"></chat-conversation>
      
    </div>
</div>

{{-- <script>
    // scroll the chat window down
    document.getElementById("chat-content").scrollTop = 100000000000;
</script>  --}}
@endsection
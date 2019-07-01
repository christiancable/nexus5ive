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
        <div class="col-md-3 d-none d-md-block">
            <ul class="list-group">
            @foreach ($conversationPartners as $partner)
                <li class="list-group-item"><a href="/chat/{{ $partner }}">{{ $partner }}</a></li>
            @endforeach
            </ul>
        </div>

        <div class="col-md-9">
        @include('chat._conversation', [$conversation])
    

        <div>
            <textarea></textarea>
        </div>
    
        </div>
    </div>


</div>
@endsection
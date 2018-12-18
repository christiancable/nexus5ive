@extends('layouts.master')

@section('meta')
<title>Messages</title>
@endsection

@section('breadcrumbs')
@include('_breadcrumbs', $breadcrumbs)
@endsection 

@section('content')

<div class="container">

@include('shared._heading', [
            $heading = 'Messages',
            $lead = 'Modem Talking'
])


    <div class="content">
        @if (count($recentMessages))
        <ul>
            @foreach ($recentMessages as $message)
            @include('messages._read', $message)
            @endforeach
        </ul>
        @endif
    </div>


    @if ($activeUsers)

        {!! Form::open(['url' => 'messages', 'class' => 'form-row']) !!}
            <div class="col">
                {!! Form::select('user_id', $activeUsers, $selected, ['class'=> 'custom-select my-1 mr-sm-2']) !!}
            </div>

            <div class="col-9">
                {!! Form::text('text', null, ['class'=> 'form-control my-1 mr-sm-2', 'autofocus']) !!}
            </div>

            <div class="col">
            {!! Form::submit('Send', ['class'=> 'btn btn-primary my-1 col-12']) !!}
            </div>
           
        {!! Form::close() !!}
    @else
     <p class="alert alert-info">
        You can't send a message because it looks like you're the only one here! *sadface*
    </p>
    @endif 
    @if ($errors->messageStore->any())
        @foreach($errors->messageStore->all() as $error)
        <p class="alert alert-danger">
            {{ $error }}
        </p>
        @endforeach
    @endif 
<hr>
    @if (count($messages))
   <div class="content">
   <h2>Archive</h2>
        <ul>
            @foreach ($messages as $message)
            @include('messages._read', $message)
            @endforeach
        </ul>
    </div>
    @endif

</div>
@endsection

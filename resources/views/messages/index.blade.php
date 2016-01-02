@extends('layouts.master')

@section('meta')
<title>Messages</title>
@endsection

@section('content')

<div class="container">


    <div class="content">
        <h1>Inbox</h1>
        <p class="lead">Modem Talking</p>
    </div>

    <hr>

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
        {!! Form::open(['url' => 'messages', 'class' => 'form-horizontal']) !!}
        <div class="form-group">
            <div class="col-md-2">
                {!!
                    Form::select('user_id', $activeUsers, $selected, ['class'=> 'form-control'])
                !!}
            </div>
            <div class="col-md-8">
                {!! Form::text('text', null, ['class'=> 'form-control', 'autofocus']) !!}
            </div>
            <div class="col-md-2">
            {!! Form::submit('Send', ['class'=> 'btn btn-primary form-control col-md-12']) !!}
            </div>
        </div>
        {!! Form::close() !!}
    @else
     <p class="alert alert-info">
        You can't send a message because it looks like you're the only one here! *sadface*
    </p>
    @endif 
    @if ($errors->any())
    <p class="alert alert-danger">
        Everytime someone sends an empty message an angel loses their wings! 
    </p>
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

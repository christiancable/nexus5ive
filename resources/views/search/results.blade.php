@extends('layouts.master')

@section('meta')
<title>Search Resuls</title>
@endsection

@section('breadcrumbs')
@include('_breadcrumbs', $breadcrumbs)
@endsection 

@section('content')

<div class="container">
@include('shared._heading', [
    $heading = 'Search', 
    $lead = 'Find old posts and half remembered dreams',
    $introduction = 'Tip: surround phrases in quotes like this _"that was no dream"_'
])
</div>


<div class="container">
{!! Form::open(['url' => 'search']) !!}
    <div class="form-group">
        {!! Form::text('text', null, ['class'=> 'form-control', 'placeholder'=>"$text", 'autofocus']) !!}
    </div>

    <div class="row">    
        <div class="col-sm-12">
            <div class="form-group">          
                {!! Form::button("<span class='oi oi-magnifying-glass'></span>&nbsp;&nbsp;Search",
                    array(
                        'type'  => 'submit',
                        'class' => "btn pull-right btn-primary col-xs-12 col-sm-3"
                        )
                ) !!}
            </div>
        </div>
    </div>
    <hr/>
{!! Form::close() !!}
</div>

@if ($errors->any())
<div class="container">
    <p class="alert alert-warning">
        If you don't look for anything then you won't find anything
    </p>
</div>
@else 

    @if($results)        
        @if($results->count() == 0)
            <div class="container">
                <p class="alert alert-info">
                    No results for <strong>{{$text}}</strong>
                </p>
            </div>
        @endif

        <?php
            $paginatedResults = $results->paginate(config('nexus.pagination'));
        ?>

        <div class="container">
            
            @foreach($paginatedResults as $result) 
                @include('search._result', $result)
            @endforeach
            {!! $paginatedResults->render() !!}
            
        </div>
    @else
        @if (isset($displaySearchResults)) 
        <div class="container">
            <p class="alert alert-warning">
                Small words like 'an', 'is', 'of' etc are excluded from the search. Please search again with different words.
            </p>
        </div>
        @endif 
    @endif

@endif
@endsection

@extends('layouts.master')

@section('meta')
<title>Search Resuls</title>
@endsection

@section('breadcrumbs')
@include('_breadcrumbs', $breadcrumbs)
@endsection 

@section('content')


<div class="container">
    <h1>Search</h1>
</div>
<hr>


<div class="container">
{!! Form::open(['url' => 'search']) !!}
    <div class="form-group">
        {!! Form::text('text', null, ['class'=> 'form-control', 'placeholder'=>"$text"]) !!}
    </div>

    <div class="row">    
    <div class="col-sm-12">
        <div class="form-group">          
            {!! Form::button("<span class='glyphicon glyphicon-search'></span>&nbsp;&nbsp;Search",
                array(
                    'type'  => 'submit',
                    'class' => "btn pull-right btn-primary col-xs-12 col-sm-3"
                    )
            ) !!}
        </div>
    </div>
</div>
{!! Form::close() !!}

</div>
<hr/>

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
        $paginatedResults = $results->paginate(env('NEXUS_PAGINATION'));
        ?>
        <div class="container">
            <div class="content">
            @foreach($paginatedResults as $result) 
                <div class="panel panel-default">
                  <div class="panel-body break-long-words">
                    <p><a href="{!! Nexus\Helpers\TopicHelper::routeToPost($result) !!}">
                     @if($result->topic->secret)
                         <strong>Anonymous</strong>
                    @else 
                        <strong>{{ $result->author->username }}</strong>
                    @endif
                    @if(!empty($result->title))
                        wrote about <em>{{$result->title}}</em>

                    @endif
                    in <strong>{{ $result->topic->title}}</strong></a><span class="text-muted"> {{ $result->time->diffForHumans() }}</span></p>
                    <p>{!! Nexus\Helpers\NxCodeHelper::nxDecode($result->text) !!}</p>
                  </div>
                </div>
            @endforeach
            {!! $paginatedResults->render() !!}
            </div>
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

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
        <p class="lead">Results for ...</p>
</div>
<hr>

<?php
$paginatedResults = $results->paginate(env('NEXUS_PAGINATION'));
?>

<div class="container">
    <div class="content">
  
    @foreach($paginatedResults as $result) 
        <div class="panel panel-default">
          <div class="panel-body">
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

@endsection

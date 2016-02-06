@extends('layouts.master')

@section('meta')
<title>{{$section->title}}</title>
@endsection

@section('content')
<div class="container">

    <div class="content">
        <h1>{{$section->title}}</h1>
        <p class="lead">{{$section->intro}}</p>
        <p>Moderated by: <a href="{{ action('Nexus\UserController@show', ['username' => $section->moderator->username])}}">{{$section->moderator->username}}</a></p>
    </div>

    @if (session('alert'))
    <div class="content">
        <div class="alert alert-warning" role="alert">No updated topics found. Why not start a new conversation or read more sections?</div>
    </div>
    @endif 

    @if (session('topic'))
    <div class="content">
        <div class="alert alert-success" role="alert">People have been talking! New posts found in <strong><a href="{{ action('Nexus\TopicController@show', ['topic_id' => session('topic')->id])}}"> {{session('topic')->title}}</a></strong></div>
    </div>
    @endif 

    <hr>

    <div class="content">
        @if (count($section->topics))
        @foreach ($section->topics as $topic)
            @if(Auth::user()->id === $section->user_id) 
                 @include('topics._edit', $topic)
                <?php $tabGroups[] ='topic'.$topic->id ?>
            @else
                @include('topics._read', $topic)
            @endif
        @endforeach
        @endif

        <?php unset($topic); ?>
        @if(Auth::user()->id === $section->user_id) 
            <div class="panel-group" id="accordion" role="tablist" aria-multiselectable="true">
              <div class="panel panel-default">
                <div class="panel-heading" role="tab" id="addNewTopic">
                  <h4 class="panel-title">
                    <a class="collapsed" role="button" data-toggle="collapse" data-parent="#accordion" href="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                      <span class='glyphicon  glyphicon-triangle-right'></span> Add New Topic
                    </a>
                  </h4>
                </div>
                <div id="collapseTwo" class="panel-collapse collapse" role="tabpanel" aria-labelledby="addNewTopic">
                  <div class="panel-body">
                     @include('topics._create', $section)
                  </div>
                </div>
              </div>
            </div>
        @endif

        @if (count($section->sections))
        <hr>
        <div class="row">
            @foreach ($section->sections as $subSection)
                @include('sections._read', $subSection)
            @endforeach
        </div>
        @endif
    </div>

</div>
@endsection

@section('javascript')
    @if (isset($tabGroups))
        @include('javascript._jqueryTabs', $tabGroups)
    @endif
@endsection
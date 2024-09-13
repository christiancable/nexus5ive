<?php
$moderator = Auth::user()->id === $section->user_id;
?>
@extends('layouts.master')

@section('meta')
<title>{{$section->title}}</title>
@endsection

@section('breadcrumbs')
@include('nexus._breadcrumbs', $breadcrumbs)
@endsection 

@section('content')

<div class="container">

    {{-- Section Heading --}}
    @if (Auth::user()->id === $section->user_id )
        <section class="d-flex flex-row justify-content-between">
            @include('shared._editToggle')
        </section>
        @include('section-heading._moderate', $section)
        
    @else
        @include('shared._heading', [
            $heading = $section->title,
            $lead = $section->intro,
            $introduction = "Moderated by: {$section->moderator->present()->profileLink}"
        ])
    @endif 


    {{-- Topics --}}
    @if (count($section->topics) > 0)
        @foreach ($section->topics as $topic)
            @if(Auth::user()->id === $section->user_id) 
                @include('topic._moderate', compact('topic', 'moderatedSections'))
            @else
                @include('topic._view', $topic)
            @endif
        @endforeach
    @endif

    {{-- Topics - add new topic --}}
    <?php unset($topic); ?>
    @if(Auth::user()->id === $section->user_id)
        @include('sections._add-topic', [$section])
    @endif
    
    {{-- Sub Sections --}}
    @if (count($section->sections) > 0)
        @include('sections._subsections', [$section, $moderator, $potentialModerators])
    @endif

    @if(Auth::user()->id === $section->user_id)
        @include('sections._add-section', [$section])
    @endif
    
    
    </div>
</div>
@endsection

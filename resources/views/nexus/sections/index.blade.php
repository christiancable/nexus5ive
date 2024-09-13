<?php
$moderator = Auth::user()->id === $section->user_id;
?>
@extends('nexus.layouts.master')

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
            @include('nexus.shared._editToggle')
        </section>
        @include('nexus.section-heading._moderate', $section)
        
    @else
        @include('nexus.shared._heading', [
            $heading = $section->title,
            $lead = $section->intro,
            $introduction = "Moderated by: {$section->moderator->present()->profileLink}"
        ])
    @endif 


    {{-- Topics --}}
    @if (count($section->topics) > 0)
        @foreach ($section->topics as $topic)
            @if(Auth::user()->id === $section->user_id) 
                @include('nexus.topic._moderate', compact('topic', 'moderatedSections'))
            @else
                @include('nexus.topic._view', $topic)
            @endif
        @endforeach
    @endif

    {{-- Topics - add new topic --}}
    <?php unset($topic); ?>
    @if(Auth::user()->id === $section->user_id)
        @include('nexus.sections._add-topic', [$section])
    @endif
    
    {{-- Sub Sections --}}
    @if (count($section->sections) > 0)
        @include('nexus.sections._subsections', [$section, $moderator, $potentialModerators])
    @endif

    @if(Auth::user()->id === $section->user_id)
        @include('nexus.sections._add-section', [$section])
    @endif
    
    
    </div>
</div>
@endsection

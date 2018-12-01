<?php
$moderator = Auth::user()->id === $section->user_id;
?>
@extends('layouts.master')

@section('meta')
<title>{{$section->title}}</title>
@endsection

@section('breadcrumbs')
@include('_breadcrumbs', $breadcrumbs)
@endsection 

@section('content')

<div class="container">

{{-- Section Heading --}}
@if (Auth::user()->id === $section->user_id )
    @include('section-heading._moderate', $section)
@else
    @include('shared._heading', [
        $heading = $section->title,
        $lead = $section->intro,
        $introduction = "Moderated by: {$section->moderator->present()->profileLink}"
    ])
@endif 

<div>
    {{-- Topics --}}
    @if (count($section->topics))
        <?php
        $moderatedSections = Auth::user()->sections->pluck('title', 'id')->toArray();
        ?>
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
    {{-- @if(Auth::user()->id === $section->user_id)
        <div class="panel-group" id="newTopicAccordion" role="tablist" aria-multiselectable="true">
            <div class="panel panel-success">
                <div class="panel-heading" role="tab" id="addNewTopic">
                    <a class="collapsed" role="button" data-toggle="collapse" data-parent="#accordion" href="#addTopic" aria-expanded="false" aria-controls="addTopic">
                        <h4 class="panel-title">
                            <span class='glyphicon glyphicon-comment'></span>&nbsp; Add New Topic
                            <i class="indicator glyphicon glyphicon-chevron-down  pull-right"></i>
                        </h4>
                    </a>
                </div>

                <div id="addTopic" class="panel-collapse collapse" role="tabpanel" aria-labelledby="addNewTopic">
                    <div class="panel-body">
                        @include('topics._create', $section)
                    </div>
                </div>
            </div>
        </div>
    @endif --}}

</div>
    
    {{-- Sub Sections --}}
    @if (count($section->sections))
        <?php
            $subSectionCount = 0;
        ?>

            @if (!$moderator)
                <div class="card-deck">
            @endif

            @foreach ($section->sections as $subSection)
                
                
                <?php $subSectionCount++; ?>
                {{-- the moderator of the parent can edit the sub sections here --}}
                @if ($moderator) 
                    <?php
                        /*
                            this section could be moved to anywhere owned by the moderator
                            minus itself and it's subsections

                            @todo this feels like too much logic happening in the view
                        */
                        $allChildSections = $subSection->allChildSections();
                        $allChildSections->push($subSection);
                        $destinations = \Auth::user()->sections->diff($allChildSections);
                        $potentialModerators = \App\User::all()->pluck('username', 'id')->toArray();
                    ?>
                    @include('section._moderate', 
                        compact('subSection', 'destinations', 'potentialModerators'))
                         {{-- don't wrap sub sections for moderators  --}}
                @else
                    @include('section._view', $subSection)

                    {{-- non-moderators get a card desk layout --}}
                    {{-- wrap sub-sections: 1 col for sm, 2 for md, 3 for lg --}}
                    <div class="w-100 d-sm-block d-md-none"></div>

                    @if ($loop->iteration %2 === 0) 
                        <div class="w-100 d-none d-md-block d-lg-none"></div>
                    @endif 

                    @if ($loop->iteration % 3 === 0)
                        <div class="w-100 d-none d-lg-block"></div>
                    @endif  
                @endif 

            @endforeach

            @if (!$moderator)
            </div> <!-- card deck -->
            @endif
        
    @endif

    
    {{-- Sub Sections - new section --}}
   <?php

   /***
    @if(Auth::user()->id === $section->user_id)
        {{-- if we have no current sections then add in the hr to separate topics and sections --}}
        @if (count($section->sections) == 0)
            <hr/>
        @endif

        <div class="panel-group" id="newSectionAccordion" role="tablist" aria-multiselectable="true">
            <div class="panel panel-success">
                <div class="panel-heading" role="tab" id="addNewSection">
                    <a class="collapsed" role="button" data-toggle="collapse" data-parent="#newSectionAccordion" href="#newSectionPanel" aria-expanded="false" aria-controls="newSectionPanel">
                        <h4 class="panel-title">
                            <span class='glyphicon  glyphicon-folder-open'></span>&nbsp; Add New Section
                            <i class="indicator glyphicon glyphicon-chevron-down  pull-right"></i>
                        </h4>
                    </a>
                </div>
            <div id="newSectionPanel" class="panel-collapse collapse" role="tabpanel" aria-labelledby="addNewSection">
            <div class="panel-body">
                @include('sections._create', $section)
            </div>
        </div>
    </div>
</div>  <!-- newSectionAccordion -->
   @endif
   */
?>
    </div>
</div>
@endsection

@section('javascript')
    @include('javascript._jqueryChevronToggles')
@endsection
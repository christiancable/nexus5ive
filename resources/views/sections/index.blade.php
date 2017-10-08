@extends('layouts.master')

@section('meta')
<title>{{$section->title}}</title>
@endsection

@section('breadcrumbs')
@include('_breadcrumbs', $breadcrumbs)
@endsection 

@section('content')
<div class="container section">

{{-- Section Heading --}}
@if (Auth::user()->id === $section->user_id )
    @include('sections._header_modify', $section)
@else
    @include('sections._header_view', $section)
@endif 

<hr>
<div class="content">
    {{-- Topics --}}
    @if (count($section->topics))
        <?php
        $moderatedSections = Auth::user()->sections->pluck('title', 'id')->toArray();
        ?>
        @foreach ($section->topics as $topic)
            @if(Auth::user()->id === $section->user_id) 
                @include('topics._edit', compact('topic', 'moderatedSections'))
            @else
                @include('topics._read', $topic)
            @endif
        @endforeach
    @endif

    {{-- Topics - add new topic --}}
    <?php unset($topic); ?>
    @if(Auth::user()->id === $section->user_id)
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
    @endif

    {{-- Sub Sections --}}
    @if (count($section->sections))
        <?php
            $subSectionCount = 0;
        ?>
        <hr>
        <div class="row">
            @foreach ($section->sections as $subSection)
                <?php $subSectionCount++; ?>
                {{-- the moderator of the parent can edit the sub sections here --}}
                @if (Auth::user()->id === $section->user_id ) 
                    <?php
                    /*
                        this section could be moved to anywhere owned by the moderator
                        minus itself and it's subsections 

                        @todo this feels like too much logic happening in the view
                    */
                    $allChildSections = \App\Helpers\SectionHelper::allChildSections($subSection);
                    $allChildSections->push($subSection);
                    $destinations = \Auth::user()->sections->diff($allChildSections);
                    ?>
                    @include('sections._subsection_moderator', compact('subSection','destinations'))
                @else
                    @include('sections._subsection_view', $subSection)
                @endif 

                {{-- force row to clear every 3 sections --}}
                @if($subSectionCount % 3 === 0)
                    <div class="clearfix"></div>
                @endif
            @endforeach
        </div>
    @endif

    {{-- Sub Sections - new section --}}
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

    </div>
</div>
@endsection

@section('javascript')
    @include('javascript._jqueryChevronToggles')
@endsection
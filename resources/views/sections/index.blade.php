@extends('layouts.master')

@section('meta')
<title>{{$section->title}}</title>
@endsection

@section('breadcrumbs')
@include('_breadcrumbs', $breadcrumbs)
@endsection 

@section('content')
<div class="container">

@if (Auth::user()->id === $section->user_id )

    <ul class="nav nav-tabs" id="section{{$section->id}}">
        <li class="active"><a href="#view">View</a></li>
        <li><a href="#settings">Settings</a></li>
    </ul>

    <div class="tab-content">
    <br/>
      <div role="tabpanel" class="tab-pane active" id="view">
        @include('sections._header_view', $section)
        <?php  $tabGroups[] = "section{$section->id}" ?>
      </div>

      <div role="tabpanel" class="tab-pane" id="settings">
        <div class="content">
        @include('sections._header_edit', $section)
        </div>
     </div>
    </div>

@else
    @include('sections._header_view', $section)
@endif 




    {{-- if you moderate the current section then show edit controls --}}
    
    
        
    <hr>

    <div class="content">
        @if (count($section->topics))
        <?php
            $moderatedSections = Auth::user()->sections->pluck('title', 'id')->toArray();
        ?>
        @foreach ($section->topics as $topic)
            @if(Auth::user()->id === $section->user_id) 
                 @include('topics._edit', compact('topic', 'moderatedSections'))
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
                      <span class='glyphicon glyphicon-comment'></span>&nbsp; Add New Topic
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
                        $allChildSections = \Nexus\Helpers\SectionHelper::allChildSections($subSection);
                        $allChildSections->push($subSection);
                        $destinations = \Auth::user()->sections->diff($allChildSections);
                    ?>
                    @include('sections._subsection_moderator', compact('subSection','destinations'))
                    <?php $tabGroups[] ='section'.$subSection->id ?>

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


        @if(Auth::user()->id === $section->user_id)
            {{-- if we have no current sections then add in the hr to separate topics and sections --}}
            @if (count($section->sections) == 0)
            <hr/>
            @endif
            <div class="panel-group" id="newSectionAccordion" role="tablist" aria-multiselectable="true">
              <div class="panel panel-default">
                <div class="panel-heading" role="tab" id="addNewSection">
                  <h4 class="panel-title">
                    <a class="collapsed" role="button" data-toggle="collapse" data-parent="#newSectionAccordion" href="#newSectionPanel" aria-expanded="false" aria-controls="newSectionPanel">
                      <span class='glyphicon  glyphicon-folder-open'></span>&nbsp; Add New Section
                    </a>
                  </h4>
                </div>
                <div id="newSectionPanel" class="panel-collapse collapse" role="tabpanel" aria-labelledby="addNewSection">
                  <div class="panel-body">
                     @include('sections._create', $section)
                  </div>
                </div>
              </div>
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
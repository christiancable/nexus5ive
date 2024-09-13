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
        // $destinations = \Auth::user()->sections->diff($allChildSections);
        $destinations = \Auth::user()->sections->diff($allChildSections)->pluck('title','id')->toArray();
        $parentSectionID = $section->id;
        ?>
        @include('section._moderate', compact('subSection', 'destinations', 'potentialModerators', 'parentSectionID'))
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
<?php
$errorBag = 'sectionUpdate' . $subSection->id;
$showErrors = count($errors->$errorBag->all());
$editTabClass = '';
$viewTabClass = 'show active';
if ($showErrors) {
    $editTabClass = 'show active';
    $viewTabClass = '';
}
?>


<div class="card border-0 bg-transparent">
{{-- tabs --}}
@include('shared._tabtop', [
  'id' => "section-" . $subSection->id,
  'viewTabId' => "subsection-view-" . $subSection->id . "-tab",
  'viewTabLink' => "#subsection-view-" . $subSection->id,
  'editTabId' => "subsection-edit-" . $subSection->id . "-tab",
  'editTabLink' => "#subsection-edit-" . $subSection->id
])

{{-- tab content --}}
<div class="tab-content" id="subsection-{{$subSection->id}}-tabContent">
  <div class="tab-pane fade {{$viewTabClass}}" id="subsection-view-{{$subSection->id}}" role="tabpanel" aria-labelledby="subsection-view-{{$subSection->id}}-tab">
    @include('section._view', $subSection)
  </div>
  <div class="tab-pane fade {{$editTabClass}}" id="subsection-edit-{{$subSection->id}}" role="tabpanel" aria-labelledby="subsection-view-{{$subSection->id}}-tab">
    @include('section._edit', $subSection)
  </div>
</div>

</div>

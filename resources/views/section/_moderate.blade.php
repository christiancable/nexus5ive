<?php
$errorBag = 'sectionUpdate' . $subSection->id;
$showErrors = count($errors->$errorBag->all());
$editNavClass = '';
$viewNavClass = 'active';
$editTabClass = '';
$viewTabClass = 'show active';
if ($showErrors) {
    $editNavClass = 'active';
    $viewNavClass = '';
    $editTabClass = 'show active';
    $viewTabClass = '';
}
?>


<div class="card border-0 bg-transparent">
{{-- tabs --}}
<ul class="nav nav-pills d-flex justify-content-end mb-1" id="section-{{$subSection->id}}" role="tablist">
  <li class="nav-item">
    <a class="nav-link {{$viewNavClass}}" id="subsection-view-{{$subSection->id}}-tab" data-toggle="tab" href="#subsection-view-{{$subSection->id}}" role="tab" aria-controls="subsection-view-{{$subSection->id}}-tab" aria-selected="true">View</a>
  </li>
  <li class="nav-item">
    <a class="nav-link {{$editNavClass}}" id="subsection-edit-{{$subSection->id}}-tab" data-toggle="tab" href="#subsection-edit-{{$subSection->id}}" role="tab" aria-controls="subsection-edit-{{$subSection->id}}-tab" aria-selected="false">Edit</a>
  </li>
</ul>

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

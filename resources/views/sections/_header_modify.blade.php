<?php
$errorBag = 'sectionUpdate' . $section->id;
$showErrors = $errors->$errorBag->all() ? true : false;

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

<ul class="nav nav-pills d-flex justify-content-end mb-1" id="section-{{$section->id}}" role="tablist">
  <li class="nav-item">
    <a class="nav-link {{$viewNavClass}}" id="section-view-{{$section->id}}-tab" data-toggle="tab" href="#section-view-{{$section->id}}" role="tab" aria-controls="section-view-{{$section->id}}-tab" aria-selected="true">View</a>
  </li>
  <li class="nav-item">
    <a class="nav-link {{$editNavClass}}" id="section-edit{{$section->id}}-tab" data-toggle="tab" href="#section-edit{{$section->id}}" role="tab" aria-controls="section-edit{{$section->id}}-tab" aria-selected="false">Edit</a>
  </li>
</ul>


{{-- tab content --}}
<div class="tab-content" id="section-{{$section->id}}-tabContent">
  <div class="tab-pane fade {{$viewTabClass}}" id="section-view-{{$section->id}}" role="tabpanel" aria-labelledby="section-view-{{$section->id}}-tab">
        @include('_heading', [
            $heading = $section->title, 
            $lead = $section->intro,
            $introduction = "Moderated by: {$section->moderator->present()->profileLink}"
        ])
  </div>
  <div class="tab-pane fade {{$editTabClass}}" id="section-edit{{$section->id}}" role="tabpanel" aria-labelledby="section-view-{{$section->id}}-tab">
      @include('sections._header_edit', $section)
  </div>
</div>
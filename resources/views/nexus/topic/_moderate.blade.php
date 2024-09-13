<?php
$errorBag = 'topicUpdate' . $topic->id;
$showErrors = count($errors->$errorBag->all());

$editTabClass = '';
$viewTabClass = 'show active';
if ($showErrors) {  
    $editTabClass = 'show active';
    $viewTabClass = '';
}
?>

{{-- tabs --}}
@include('shared._tabtop', [
  'id' => "topic-" . $topic->id,
  'viewTabId' => "view-view-" . $section->id . "-tab",
  'viewTabLink' => "#view-" . $topic->id,
  'editTabId' => "edit-" . $topic->id . "-tab",
  'editTabLink' => "#edit-" . $topic->id
])

{{-- tab content --}}
<div class="tab-content" id="topic-{{$topic->id}}-tabContent">
  <div class="tab-pane fade {{$viewTabClass}}" id="view-{{$topic->id}}" role="tabpanel" aria-labelledby="view-{{$topic->id}}-tab">
    @include('topic._view', $topic)
  </div>
  <div class="tab-pane fade {{$editTabClass}}" id="edit-{{$topic->id}}" role="tabpanel" aria-labelledby="view-{{$topic->id}}-tab">
    @include('topic._edit', [$topic, $moderatedSections])
  </div>
</div>
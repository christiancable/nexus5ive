<?php
$errorBag = 'topicUpdate' . $topic->id;
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

{{-- tabs --}}
<ul class="nav nav-pills d-flex justify-content-end mb-1" id="topic-{{$topic->id}}" role="tablist">
  <li class="nav-item">
    <a class="nav-link {{$viewNavClass}}" id="view-{{$topic->id}}-tab" data-toggle="tab" href="#view-{{$topic->id}}" role="tab" aria-controls="view-{{$topic->id}}-tab" aria-selected="true">View</a>
  </li>
  <li class="nav-item">
    <a class="nav-link {{$editNavClass}}" id="edit-{{$topic->id}}-tab" data-toggle="tab" href="#edit-{{$topic->id}}" role="tab" aria-controls="edit-{{$topic->id}}-tab" aria-selected="false">Edit</a>
  </li>
</ul>

{{-- tab content --}}
<div class="tab-content" id="topic-{{$topic->id}}-tabContent">
  <div class="tab-pane fade {{$viewTabClass}}" id="view-{{$topic->id}}" role="tabpanel" aria-labelledby="view-{{$topic->id}}-tab">
    @include('topics._read', $topic)
  </div>
  <div class="tab-pane fade {{$editTabClass}}" id="edit-{{$topic->id}}" role="tabpanel" aria-labelledby="view-{{$topic->id}}-tab">
    @include('topics._update', $topic)
  </div>
</div>
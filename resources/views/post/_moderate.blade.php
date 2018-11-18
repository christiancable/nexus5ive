<?php
$errorBag = 'postUpdate' . $post->id;
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
<ul class="nav nav-pills d-flex justify-content-end mb-1" id="post-{{$post->id}}" role="tablist">

  <li class="nav-item">
    <a class="nav-link {{$viewNavClass}}" id="view-{{$post->id}}-tab" data-toggle="tab" href="#view-{{$post->id}}" role="tab" aria-controls="view-{{$post->id}}-tab" aria-selected="true">View</a>
  </li>
  <li class="nav-item">
    <a class="nav-link {{$editNavClass}}" id="view-{{$post->id}}-tab" data-toggle="tab" href="#edit-{{$post->id}}" role="tab" aria-controls="edit-{{$post->id}}-tab" aria-selected="false">Edit</a>
  </li>
</ul>

{{-- tab content --}}
<div class="tab-content" id="post-{{$post->id}}-tabContent">
  <div class="tab-pane fade {{$viewTabClass}}" id="view-{{$post->id}}" role="tabpanel" aria-labelledby="view-{{$post->id}}-tab">
    @include('post._view', $post)
  </div>
  <div class="tab-pane fade {{$editTabClass}}" id="edit-{{$post->id}}" role="tabpanel" aria-labelledby="view-{{$post->id}}-tab">
     @include('post._edit', [$post, $allowDelete])
  </div>
</div>
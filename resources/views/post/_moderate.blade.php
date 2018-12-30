<?php
$errorBag = 'postUpdate' . $post->id;
$showErrors = count($errors->$errorBag->all());
$editTabClass = '';
$viewTabClass = 'show active';
if ($showErrors) {    
    $editTabClass = 'show active';
    $viewTabClass = '';
}
?>

{{-- tab top --}}
@include('shared._tabtop', [
  'id' => "post-" . $post->id,
  'viewTabId' => "view-" . $post->id . "-tab",
  'viewTabLink' => "#view-" . $post->id,
  'editTabId' => "edit-" . $post->id . "-tab",
  'editTabLink' => "#edit-" . $post->id,
])

{{-- tab content --}}
<div class="tab-content" id="post-{{$post->id}}-tabContent">
  <div class="tab-pane fade {{$viewTabClass}}" id="view-{{$post->id}}" role="tabpanel" aria-labelledby="view-{{$post->id}}-tab">
    @include('post._view', $post)
  </div>
  <div class="tab-pane fade {{$editTabClass}}" id="edit-{{$post->id}}" role="tabpanel" aria-labelledby="view-{{$post->id}}-tab">
     @include('post._edit', [$post, $allowDelete])
  </div>
</div>
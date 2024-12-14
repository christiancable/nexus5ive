<?php

$authorLink = "{$post->author->present()->profileLink} &ndash; {$post->popname}";
if ($post->topic->secret && $userCanSeeSecrets == false) {
    $authorLink = '<span><strong>Unknown User &ndash; Unknown User</strong></span>';
}

$postTime = $post->time;
$formattedTime = date('D, F jS Y - H:i', strtotime($post->time));
if ($post->topic->secret && $userCanSeeSecrets == false) {
    // if we are anonymous them we want to see fuzzy times
    $formattedTime = $post->time->diffForHumans();
}

$timeClass = 'text-muted';
if ($readProgress < $postTime) {
    $timeClass = 'text-info';
}

$editedByInfo = null;
if ($post->editor) {
    // if we are anonymous them we want to see fuzzy times
    if ($post->topic->secret && $userCanSeeSecrets == false) {
        $editedByInfo = "Edited by <strong>Anonymous</strong> around {$post->updated_at->diffForHumans()}";
    } else {
        $editedByInfo = "Edited by <strong>{$post->editor->username}</strong> at {$post->updated_at->format('D, F jS Y - H:i')}";
    }
}
?>
<div class="card mb-3" id="{{$post->id}}">
    @if ($post->title)
        <div class="card-header bg-primary text-white">
            <span class="card-title">{{$post->title}}</span>
        </div>
    @endif

    <div class="card-body">
        <div class="d-flex justify-content-between">
        <span>{!! $authorLink !!}</span>
        <small class="{{$timeClass}}">{{$formattedTime}}</small>
        </div>
        <p class="card-text">
            <hr>
            {!! App\Helpers\NxCodeHelper::nxDecode($post->text) !!}
        </p>
        @if ($editedByInfo)
            <footer class="d-flex justify-content-end">
                <small class="text-muted">{!! $editedByInfo !!}</small>
            </footer>
        @endif
    </div>
</div>

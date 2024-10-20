<?php
$errorBag = 'sectionUpdate' . $section->id;
$showErrors = $errors->$errorBag->all() ? true : false;
$editTabClass = '';
$viewTabClass = 'show active';
if ($showErrors) {
    $editTabClass = 'show active';
    $viewTabClass = '';
}
?>

{{-- tab top --}}
@include('nexus.shared._tabtop', [
    'id' => 'section-' . $section->id,
    'viewTabId' => 'section-view-' . $section->id . '-tab',
    'viewTabLink' => '#section-view-' . $section->id,
    'editTabId' => 'section-edit' . $section->id . '-tab',
    'editTabLink' => '#section-edit' . $section->id,
])

{{-- tab content --}}
<div class="tab-content" id="section-{{ $section->id }}-tabContent">
    <div class="tab-pane fade {{ $viewTabClass }}" id="section-view-{{ $section->id }}" role="tabpanel"
        aria-labelledby="section-view-{{ $section->id }}-tab">

        <x-heading heading="{{ $section->title }}" lead="{{ $section->intro }}"
            introduction="Moderated by: {!! $section->moderator->present()->profileLink !!}" />

    </div>
    <div class="tab-pane fade {{ $editTabClass }}" id="section-edit{{ $section->id }}" role="tabpanel"
        aria-labelledby="section-view-{{ $section->id }}-tab">
        @include('nexus.section-heading._edit', $section)
    </div>
</div>

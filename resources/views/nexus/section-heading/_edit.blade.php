<?php
    $formName = 'section' . $section->id;
    $errorBag = 'sectionUpdate' . $section->id;
?>

<form action="{{ route('section.update', $section->id) }}" method="POST" class="form" name="{{ $formName }}">
    @csrf
    @method('PATCH')

    <input type="hidden" name="id" value="{{ $section->id }}">
    <input type="hidden" name="form[{{ $formName }}][id]" value="{{ $section->id }}">
    <input type="hidden" name="form[{{ $formName }}][parent_id]" value="{{ $section->parent_id }}">
    <input type="hidden" name="form[{{ $formName }}][user_id]" value="{{ $section->user_id }}">
    <input type="hidden" name="form[{{ $formName }}][weight]" value="{{ $section->weight }}">

    <div class="mb-3">
        <input type="text" name="form[{{ $formName }}][title]" value="{{ $section->title }}" class="form-control" placeholder="Title">
    </div>

    <div class="mb-3">
        <textarea name="form[{{ $formName }}][intro]" class="form-control">{{ $section->intro }}</textarea>
    </div>

    <input type="hidden" name="form[{{ $formName }}][allow_user_topics]" value="0">
    <div class="mb-3">
        <div class="form-check form-switch">
            <input class="form-check-input" type="checkbox"
                   id="allow_user_topics_{{ $section->id }}"
                   name="form[{{ $formName }}][allow_user_topics]"
                   value="1"
                   {{ $section->allow_user_topics ? 'checked' : '' }}>
            <label class="form-check-label" for="allow_user_topics_{{ $section->id }}">
                Allow all users to create topics
            </label>
        </div>
    </div>

    <div class="d-flex justify-content-end">
        <div class="mb-3">
            <x-ui.button variant="success" type="submit">
                <x-heroicon-s-pencil class="icon_mini me-1" aria-hidden="true" />Save Changes
            </x-ui.button>
        </div>
    </div>
</form>

@if ($errors->$errorBag->any())
    @include('nexus.forms._errors', ['errors' => $errors->$errorBag->all()])
@endif

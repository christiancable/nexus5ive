<?php
$formName = 'topicUpdate' . $topic->id;
$errorBag = 'topicUpdate' . $topic->id;
?>

<form action="{{ route('topic.update', $topic->id) }}" method="POST" class="form">
    @csrf
    @method('PATCH')

    <input type="hidden" name="id" value="{{ $topic->id }}">
    <input type="hidden" name="{{ $formName }}[id]" value="{{ $topic->id }}">
    <input type="hidden" name="{{ $formName }}[secret]" value="0">
    <input type="hidden" name="{{ $formName }}[readonly]" value="0">

    <div class="mb-3">
        <label for="title" class="hidden">Title</label>
        <input type="text" name="{{ $formName }}[title]" value="{{ $topic->title }}" class="form-control"
            placeholder="Title">
    </div>

    <div class="mb-3">
        <label for="intro" class="hidden">Introduction</label>
        <textarea name="{{ $formName }}[intro]" class="form-control" rows="3" placeholder="Introduction">{{ $topic->intro }}</textarea>
    </div>

    <div class="d-md-flex justify-content-md-between">
        <fieldset>
            <div class="form-check">
                @if ($topic->secret)
                    <input class="form-check-input" type="checkbox" value="1" id="{{ $formName }}[secret]"
                        name="{{ $formName }}[secret]" checked>
                @else
                    <input class="form-check-input" type="checkbox" value="1" id="{{ $formName }}[secret]"
                        name="{{ $formName }}[secret]">
                @endif
                <label class="form-check-label" for="{{ $formName }}[secret]">Anonymous</label>
            </div>

            <div class="form-check">
                @if ($topic->readonly)
                    <input class="form-check-input" type="checkbox" value="1" id="{{ $formName }}[readonly]"
                        name="{{ $formName }}[readonly]" checked>
                @else
                    <input class="form-check-input" type="checkbox" value="1" id="{{ $formName }}[readonly]"
                        name="{{ $formName }}[readonly]">
                @endif
                <label class="form-check-label" for="{{ $formName }}[readonly]">Read Only</label>
            </div>
        </fieldset>

        @if (isset($moderatedSections))
            <div class="mb-3 d-flex align-items-center">
                <label for="{{ $formName }}[section_id]" class="me-sm-2">Section</label>
                <select name="{{ $formName }}[section_id]" class="form-select form-select">
                    @foreach ($moderatedSections as $id => $title)
                        <option value="{{ $id }}" {{ $topic->section_id == $id ? 'selected' : '' }}>
                            {{ $title }}</option>
                    @endforeach
                </select>
            </div>
        @endif

        <div class="mb-3 d-flex align-items-center">
            <label for="{{ $formName }}[weight]" class="me-sm-2">Order</label>
            <select name="{{ $formName }}[weight]" class="form-select form-select">
                @for ($i = 0; $i <= 10; $i++)
                    <option value="{{ $i }}" {{ $topic->weight == $i ? 'selected' : '' }}>
                        {{ $i }}</option>
                @endfor
            </select>
        </div>
    </div>

    <div class="d-flex flex-row-reverse justify-content-between bd-highlight">
        <div class="mb-3 ms-2">
            <x-ui.button variant="success" type="submit">
                <x-heroicon-s-pencil class="icon_mini me-1" aria-hidden="true" />Save Changes
            </x-ui.button>
        </div>

</form>

<form action="{{ action('App\Http\Controllers\Nexus\TopicController@destroy', ['topic' => $topic->id]) }}"
    method="POST">
    @csrf
    @method('DELETE')

        <div class="mb-3">
            <x-ui.button variant="warning" type="submit">
                <x-heroicon-s-archive-box-arrow-down class="icon_mini me-1" aria-hidden="true" />
                Archive Topic
            </x-ui.button>
        </div>

</form>
</div>


@if ($errors->$errorBag->any())
    @include('nexus.forms._errors', ['errors' => $errors->$errorBag->all()])
@endif

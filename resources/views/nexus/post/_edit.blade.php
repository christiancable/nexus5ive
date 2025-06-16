<?php
$formName = $post->id;
$errorBag = 'postUpdate' . $post->id;
?>

<div class="mb-3">
    <form action="{{ route('posts.update', $post->id) }}" method="POST" class="form" name="{{ $formName }}">
        @csrf
        @method('PATCH')
        <input type="hidden" name="id" value="{{ $post->id }}">

        <div class="mb-3">
            <input type="text" name="form[{{ $formName }}][title]" value="{{ $post->title }}"
                class="form-control" placeholder="Subject">
        </div>

        <div class="mb-3">
            <textarea name="form[{{ $formName }}][text]" class="form-control">{{ $post->text }}</textarea>
        </div>

        <div class="d-flex flex-row-reverse justify-content-between bd-highlight">
            <div class="mb-3 ms-2">
                <x-ui.button variant="success" type="submit">
                    <x-heroicon-s-pencil class="icon_mini me-1" aria-hidden="true" />Save Changes
                </x-ui.button>
            </div>
        </form>
        
        @if ($allowDelete)
        <form action="{{ route('posts.destroy', ['post' => $post->id]) }}" method="POST">
            @csrf
            @method('DELETE')
            <div class="mb-3">
                <x-ui.button variant="danger" type="submit">
                    <x-heroicon-s-trash class="icon_mini me-1" aria-hidden="true" />Delete
                </x-ui.button>
            </div>
        </form>
    @endif
        
        
        </div>
        
</div>

@if ($errors->$errorBag->any())
    @include('nexus.forms._errors', ['errors' => $errors->$errorBag->all()])
@endif

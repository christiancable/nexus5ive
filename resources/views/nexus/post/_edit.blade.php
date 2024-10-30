<?php
$formName = $post->id;
$errorBag = 'postUpdate' . $post->id;
?>

<div class="mb-3">
    <form action="{{ route('posts.update', $post->id) }}" method="POST" class="form" name="{{ $formName }}">
        @csrf
        @method('PATCH')
        <input type="hidden" name="id" value="{{ $post->id }}">

        <div class="form-group">
            <input type="text" name="form[{{ $formName }}][title]" value="{{ $post->title }}"
                class="form-control" placeholder="Subject">
        </div>

        <div class="form-group">
            <textarea name="form[{{ $formName }}][text]" class="form-control">{{ $post->text }}</textarea>
        </div>

        <div class="d-flex flex-row-reverse justify-content-between bd-highlight">
            <div class="form-group ml-2">
                <x-button class="btn-success" type="success">
                    <x-heroicon-s-pencil class="icon_mini mr-1" aria-hidden="true" />Save Changes
                </x-button>
            </div>
        </form>
        
        @if ($allowDelete)
        <form action="{{ route('posts.destroy', ['post' => $post->id]) }}" method="POST">
            @csrf
            @method('DELETE')
            <div class="form-group">
                <x-button class="btn-danger" type="success">
                    <x-heroicon-s-trash class="icon_mini mr-1" aria-hidden="true" />Delete
                </x-button>
            </div>
        </form>
    @endif
        
        
        </div>
        
</div>

@if ($errors->$errorBag->any())
    @include('nexus.forms._errors', ['errors' => $errors->$errorBag->all()])
@endif

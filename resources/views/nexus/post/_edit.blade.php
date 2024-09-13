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
            <input type="text" name="form[{{ $formName }}][title]" value="{{ $post->title }}" class="form-control" placeholder="Subject">
        </div>

        <div class="form-group">
            <textarea name="form[{{ $formName }}][text]" class="form-control">{{ $post->text }}</textarea>
        </div>

        <div class="d-flex flex-row-reverse bd-highlight">
            <div class="form-group ml-2">
                <button type="submit" class="btn btn-success">
                    <span class='oi oi-pencil mr-2'></span>Save Changes
                </button>
            </div>
        </div>
    </form>

    @if ($allowDelete)
        <form action="{{ route('posts.destroy', ['post' => $post->id]) }}" method="POST">
            @csrf
            @method('DELETE')
            <div class="form-group">
                <button type="submit" class="btn btn-danger">
                    <span class='oi oi-delete mr-2'></span>Delete
                </button>
            </div>
        </form>
    @endif
</div>

@if ($errors->$errorBag->any())
    @include('nexus.forms._errors', ['errors' => $errors->$errorBag->all()])
@endif

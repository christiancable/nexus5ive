<form action="{{ route('section.store') }}" method="POST" class="form">
    @csrf
    <input type="hidden" name="parent_id" value="{{ $section->id }}">

    <div class="form-group">
        <label for="title" class="sr-only">Title</label>
        <input type="text" name="title" class="form-control" placeholder="Title">
    </div>
    <div class="form-group">
        <label for="intro" class="sr-only">Introduction</label>
        <textarea name="intro" class="form-control" rows="3" placeholder="Introduction"></textarea>
    </div>

    <div class="d-flex flex-row-reverse bd-highlight">
        <div class="form-group ml-2">
            <x-button type="submit" class="btn btn-success">
                <x-heroicon-s-folder-plus class="icon_mini mr-1" aria-hidden="true" />Add Section
            </x-button>
        </div>
    </div>
</form>

@if ($errors->sectionCreate->any())
    @include('nexus.forms._errors', [
        'errors' => $errors->sectionCreate->all(),
        'formContainer' => 'addSection',
    ])
@endif

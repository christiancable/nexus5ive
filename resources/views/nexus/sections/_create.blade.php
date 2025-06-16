<form action="{{ route('section.store') }}" method="POST" class="form">
    @csrf
    <input type="hidden" name="parent_id" value="{{ $section->id }}">

    <div class="mb-3">
        <label for="title" class="visually-hidden">Title</label>
        <input type="text" name="title" class="form-control" placeholder="Title">
    </div>
    <div class="mb-3">
        <label for="intro" class="visually-hidden">Introduction</label>
        <textarea name="intro" class="form-control" rows="3" placeholder="Introduction"></textarea>
    </div>

    <div class="d-flex flex-row-reverse bd-highlight">
        <div class="mb-3 ms-2">
            <x-ui.button variant="success" type="submit">
                <x-heroicon-s-folder-plus class="icon_mini me-1" aria-hidden="true" />Add Section
            </x-ui.button>
        </div>
    </div>
</form>

@if ($errors->sectionCreate->any())
    @include('nexus.forms._errors', [
        'errors' => $errors->sectionCreate->all(),
        'formContainer' => 'addSection',
    ])
@endif

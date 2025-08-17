<form action="{{ route('topic.store') }}" method="POST" class="form">
    @csrf
    <input type="hidden" name="section_id" value="{{ $section->id }}">
    <input type="hidden" name="secret" value="0">
    <input type="hidden" name="readonly" value="0">

    <div class="mb-3">
        <label for="title" class="visually-hidden">Title</label>
        <input type="text" name="title" class="form-control" placeholder="Title">
    </div>
    <div class="mb-3">
        <label for="intro" class="visually-hidden">Introduction</label>
        <textarea name="intro" class="form-control" rows="3" placeholder="Introduction"></textarea>
    </div>

    <div class="d-md-flex justify-content-md-between">
        <fieldset>
            <div class="form-check">
                <input class="form-check-input" type="checkbox" value="1" id="secret" name="secret">
                <label class="form-check-label" for="secret">Anonymous</label>
            </div>

            <div class="form-check">
                <input class="form-check-input" type="checkbox" value="1" id="readonly" name="readonly">
                <label class="form-check-label" for="readonly">Read Only</label>
            </div>
        </fieldset>

        <div class="mb-3 d-flex align-items-center">
            <label for="weight" class="me-sm-2">Order</label>
            <select name="weight" class="form-select form-select">
                @for ($i = 0; $i <= 10; $i++)
                    <option value="{{ $i }}">{{ $i }}</option>
                @endfor
            </select>
        </div>
    </div>

    <div class="d-flex flex-row-reverse bd-highlight">
        <div class="mb-3 ms-2">
            <x-ui.button variant="success" type="submit">
                <x-heroicon-s-document-plus class="icon_mini me-1" aria-hidden="true" />Add Topic
            </x-ui.button>
        </div>
    </div>
</form>

@if ($errors->topicCreate->all())
    @include('nexus.forms._errors', [
        'errors' => $errors->topicCreate->all(),
        'formContainer' => 'addTopic',
    ])
@endif

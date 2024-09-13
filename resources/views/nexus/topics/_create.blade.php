<form action="{{ route('topic.store') }}" method="POST" class="form">
    @csrf
    <input type="hidden" name="section_id" value="{{ $section->id }}">
    <input type="hidden" name="secret" value="0">
    <input type="hidden" name="readonly" value="0">

    <div class="form-group">    
        <label for="title" class="sr-only">Title</label>
        <input type="text" name="title" class="form-control" placeholder="Title">
    </div>
    <div class="form-group">
        <label for="intro" class="sr-only">Introduction</label>
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

        <div class="form-group form-inline">
            <label for="weight" class="mr-sm-2">Order</label>
            <select name="weight" class="form-control custom-select">
                @for ($i = 0; $i <= 10; $i++)
                    <option value="{{ $i }}">{{ $i }}</option>
                @endfor
            </select>
        </div>
    </div>

    <div class="d-flex flex-row-reverse bd-highlight">    
        <div class="form-group ml-2">          
            <button type="submit" class="btn btn-success">
                <span class='oi oi-plus mr-2'></span>Add Topic
            </button>
        </div>
    </div>
</form>

@if ($errors->topicCreate->all())
    @include('forms._errors', ['errors' => $errors->topicCreate->all(), 'formContainer' => 'addTopic'])
@endif

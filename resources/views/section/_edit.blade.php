@php
    $errorBag = 'sectionUpdate' . $subSection->id;
    $formName = 'section' . $subSection->id;
@endphp

{{-- this is for moderators to edit sub sections --}}
<div class="card border-0 bg-transparent">
    <form action="{{ route('section.update', $subSection->id) }}" method="POST" class="form" name="{{ $formName }}">
        @csrf
        @method('PATCH')

        <input type="hidden" name="id" value="{{ $subSection->id }}">
        <input type="hidden" name="form[{{ $formName }}][id]" value="{{ $subSection->id }}">

        <div class="form-group">
            <input type="text" name="form[{{ $formName }}][title]" value="{{ $subSection->title }}" class="form-control" placeholder="Title">
        </div>

        <div class="form-group">
            <textarea name="form[{{ $formName }}][intro]" class="form-control">{{ $subSection->intro }}</textarea>
        </div>

        <div class="d-md-flex justify-content-md-between">
            <div class="form-group form-inline">
                <label class="mr-sm-2" for="{{ $formName }}[user_id]">Moderator</label>
                <select name="form[{{ $formName }}][user_id]" class="form-control custom-select">
                    @foreach($potentialModerators as $id => $name)
                        <option value="{{ $id }}" {{ $subSection->moderator->id == $id ? 'selected' : '' }}>{{ $name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="form-group form-inline">
                <label class="mr-sm-2" for="{{ $formName }}[parent_id]">Section</label>
                <select name="form[{{ $formName }}][parent_id]" class="form-control custom-select">
                    @foreach($destinations as $id => $title)
                        <option value="{{ $id }}" {{ $parentSectionID == $id ? 'selected' : '' }}>{{ $title }}</option>
                    @endforeach
                </select>
            </div>

            <div class="form-group form-inline">
                <label class="mr-sm-2" for="{{ $formName }}[weight]">Order</label>
                <select name="form[{{ $formName }}][weight]" class="form-control custom-select">
                    @for ($i = 0; $i <= 10; $i++)
                        <option value="{{ $i }}" {{ $subSection->weight == $i ? 'selected' : '' }}>{{ $i }}</option>
                    @endfor
                </select>
            </div>
        </div>

        <div class="d-flex flex-row-reverse bd-highlight">
            <div class="form-group ml-2">
                <button type="submit" class="btn btn-success">
                    <span class='oi oi-pencil mr-2'></span>Save Changes
                </button>
            </div>
        </div>
    </form>

    <form action="{{ action('Nexus\\SectionController@destroy', ['section' => $subSection->id]) }}" method="POST">
        @csrf
        @method('DELETE')
        <div class="form-group">
            <button type="submit" class="btn btn-warning">
                <span class='oi oi-box mr-2'></span>Archive Section
            </button>
        </div>
    </form>
</div>

@if ($errors->$errorBag->any())
    @include('forms._errors', ['errors' => $errors->$errorBag->all()])
@endif
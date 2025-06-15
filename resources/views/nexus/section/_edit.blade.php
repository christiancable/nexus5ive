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

        <div class="mb-3">
            <input type="text" name="form[{{ $formName }}][title]" value="{{ $subSection->title }}"
                class="form-control" placeholder="Title">
        </div>

        <div class="mb-3">
            <textarea name="form[{{ $formName }}][intro]" class="form-control">{{ $subSection->intro }}</textarea>
        </div>

        <div class="d-md-flex justify-content-md-between">
            <div class="mb-3 d-flex align-items-center">
                <label class="me-sm-2" for="{{ $formName }}[user_id]">Moderator</label>
                <select name="form[{{ $formName }}][user_id]" class="form-select form-select">
                    @foreach ($potentialModerators as $id => $name)
                        <option value="{{ $id }}" {{ $subSection->moderator->id == $id ? 'selected' : '' }}>
                            {{ $name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="mb-3 d-flex align-items-center">
                <label class="me-sm-2" for="{{ $formName }}[parent_id]">Section</label>
                <select name="form[{{ $formName }}][parent_id]" class="form-select form-select">
                    @foreach ($destinations as $id => $title)
                        <option value="{{ $id }}" {{ $parentSectionID == $id ? 'selected' : '' }}>
                            {{ $title }}</option>
                    @endforeach
                </select>
            </div>

            <div class="mb-3 d-flex align-items-center">
                <label class="me-sm-2" for="{{ $formName }}[weight]">Order</label>
                <select name="form[{{ $formName }}][weight]" class="form-select form-select">
                    @for ($i = 0; $i <= 10; $i++)
                        <option value="{{ $i }}" {{ $subSection->weight == $i ? 'selected' : '' }}>
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

            <form action="{{ action('App\Http\Controllers\Nexus\\SectionController@destroy', ['section' => $subSection->id]) }}" method="POST">
                @csrf
                @method('DELETE')
            <div class="mb-3">
                <x-ui.button variant="warning" type="submit">
                    <x-heroicon-s-archive-box-arrow-down class="icon_mini me-1" aria-hidden="true" />
                    Archive Section
                </x-ui.button>
            </div>
            </form>

        </div>

    
</div>

@if ($errors->$errorBag->any())
    @include('nexus.forms._errors', ['errors' => $errors->$errorBag->all()])
@endif

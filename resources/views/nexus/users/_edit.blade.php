@if ($errors->userUpdate->any())
    @include('forms._errors', ['errors' => $errors->userUpdate->all()])
@endif
<form action="{{ route('users.update', $user->username) }}" method="POST" class="form" enctype="multipart/form-data">
    @csrf
    @method('PATCH')

    <input type="hidden" name="id" value="{{ $user->id }}">

<div class="form-row">
    <div class="form-group col-md-6">
        <label for="name">Name</label>
        <input type="text" name="name" value="{{ old('name', $user->name) }}" class="form-control">
    </div>
    <div class="form-group col-md-6">
        <label for="location">Location</label>
        <input type="text" name="location" value="{{ old('location', $user->location) }}" class="form-control">
    </div>
</div>


 <div class="form-row">
    <div class="form-group col-md-6">
        <label for="popname">Popname</label>
        <input type="text" name="popname" value="{{ old('popname', $user->popname) }}" class="form-control">
    </div>
    <div class="form-group col-md-6">
        <label for="favouriteMovie">Favourite Film</label>
        <input type="text" name="favouriteMovie" value="{{ old('favouriteMovie', $user->favouriteMovie) }}" class="form-control">
    </div>
</div>


<div class="form-row">
    <div class="form-group col-md-6">
        <label for="email">Email</label>
        <input type="email" name="email" value="{{ old('email', $user->email) }}" class="form-control" autocomplete="off">
    </div>
    <div class="form-group col-md-6">
        <label for="favouriteMusic">Favourite Band</label>
        <input type="text" name="favouriteMusic" value="{{ old('favouriteMusic', $user->favouriteMusic) }}" class="form-control">
    </div>
</div>


<div class="form-row">
    <div class="form-check">
        <input type="hidden" name="private" value="0">
        <input type="checkbox" name="private" value="1" {{ $user->private ? 'checked' : '' }} class="form-check-input" id="private">
        <label for="private" class="form-check-label">Hide Email</label>
    </div>
</div>

<hr>

<div class="form-row form-inline">
    <div class="form-group col-12 col-md-6">
        <label for="theme" class="mr-3">Theme</label>
        <select name="theme_id" class="form-control custom-select" dusk="theme_select">
            @foreach($themes as $themeId => $themeName)
                <option value="{{ $themeId }}" {{ $user->theme->id == $themeId ? 'selected' : '' }}>{{ $themeName }}</option>
            @endforeach
        </select>
    </div>
    <div class="form-group col-12 col-md-6">
        <div class="form-check">
            <input type="hidden" name="viewLatestPostFirst" value="0">
            <input type="checkbox" name="viewLatestPostFirst" value="1" {{ $user->viewLatestPostFirst ? 'checked' : '' }} class="form-check-input" id="viewLatestPostFirst">
            <label for="viewLatestPostFirst" class="form-check-label">Show Latest Posts First</label>
        </div>
    </div>
</div>



<hr>

<div class="row mb-3">
    <div class="col">
        <div class="form-group">
            <label for="password" class="d-block">New Password</label>
            <input type="password" name="password" class="form-control">
        </div>
        <div class="form-group">
            <label for="password_confirmation" class="d-block">Confirm New Password</label>
            <input type="password" name="password_confirmation" class="form-control">
        </div>
    </div>

    <div class="col">
        @include('users._score', $user)
    </div>
</div>

<div class="form-group">
    <label for="about">About</label>
    <textarea name="about" class="form-control" cols="50" rows="10">{{ old('about', $user->about) }}</textarea>
</div>
   
<div class="form-group">
    <button type="submit" class="btn btn-warning form-control">Save Changes</button>
</div>
</form>


@if (count($user->sections))
    <span>You moderate the following sections </span>
    <!-- Single button -->


    <div class="btn-group">
        <button type="button" class="btn btn-secondary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            Choose Section <span class="caret"></span>
        </button>
        <div class="dropdown-menu">
            @foreach ($user->sections as $section)
                <a class="dropdown-item" href="{{ action('App\Http\Controllers\Nexus\SectionController@show', ['section' => $section->id]) }}">{{$section->title}}</a>
            @endforeach
        </div>
    </div>
    <hr> 
@endif
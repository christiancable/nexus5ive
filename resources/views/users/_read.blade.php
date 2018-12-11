
<div class="row">

    <dl class="col">        
    @if ($user->private != true)
        <dt>Email</dt><dd><a href="mailto:{{$user->email}}">{{$user->email}}</a></dd>
    @else
        <dt>Email</dt><dd><em>Hidden</em></dd>
    @endif
        <dt>Location</dt><dd>{{$user->location}}</dd>
        <dt>Favourite Film</dt><dd>{{$user->favouriteMovie}}</dd>
        <dt>Favourite Band</dt><dd>{{$user->favouriteMusic}}</dd>
    </dl>

    <dl class="col">        
    @if ($user->latestLogin)
        <dt>Latest Visit</dt><dd>{{$user->latestLogin->diffForHumans()}}</dd>
    @else
        <dt>Latest Visit</dt><dd>Never</dd>
    @endif
        <dt>Total Posts</dt><dd>{{$user->totalPosts}}</dd>
        <dt>Total Visits</dt><dd>{{$user->totalVisits}}</dd>
    </dl>

</div>

<div class="card mb-3 bg-light">
    <div class="card-body border border-light rounded">
        <div class="card-text">
        {!! App\Helpers\NxCodeHelper::nxDecode($user->about) !!}
        </div>
    </div>
</div>


@if (count($user->sections))
<span>If you like <strong>{{$user->username}}</strong> then check out these sections they moderate </span>
<div class="dropdown d-inline">
    <button class="btn btn-secondary dropdown-toggle" type="button" id="sectionMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
        Choose Section
    </button>
    <div class="dropdown-menu" aria-labelledby="sectionMenuButton">
        @foreach ($user->sections as $section)
        <a class="dropdown-item" href="{{ action('Nexus\SectionController@show', ['section_id' => $section->id]) }}">{{$section->title}}</a>
        @endforeach        
    </div>
</div>
<hr> 
@endif
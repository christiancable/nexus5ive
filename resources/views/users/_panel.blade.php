<div class="card text-center mb-3 bg-light">
    <div class="card-header bg-dark text-white">
        <a href="{{action('Nexus\UserController@show', ['user' => $user->username]) }}" class="d-block text-white"><h3 class="card-title mb-0">{{$user->username}}</h3></a>
    </div>
    <div class="card-body">
        @if($user->name)
        <p class="card-subtitle">{{$user->name}}</p>
        @endif

        <p class="card-text text-secondary">
            {!! ($user->popname) ? "<q><em>$user->popname</em></q>" : "<br>" !!}
        </p>

        <div class="row text-secondary mb-3">
            <div class="col">
                <p class="h2 mb-0 text-dark">{{$user->totalPosts}}</p>
                Posts 
            </div>
            <div class="col">
                <p class="h2 mb-0 text-dark">{{$user->totalVisits}}</p>
                Visits
            </div>
        </div>

        <p><a href="{{action('Nexus\UserController@show', ['user' => $user->username]) }}" class="btn btn-primary">View Profile</a></p>
    </div>

    @if ($user->latestLogin)
    <div class="card-footer text-muted">
        <small>Latest Visit {{$user->latestLogin->diffForHumans()}}</small>
    </div>
    @endif
</div>
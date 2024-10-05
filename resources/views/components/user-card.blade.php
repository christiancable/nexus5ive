@props(['user'])
<div class="col-12 col-md-4 mb-4">
    <div class="card text-center mb-3 bg-light h-100">
        <div class="card-header text-white bg-info"><a href="/users/{{ $user->username }}" class="d-block text-white">
                <h3 class="card-title mb-0">{{ $user->username }}</h3>
            </a></div>
        <div class="card-body">
            <p class="card-subtitle">{{ $user->name }}</p>
            <p class="card-text text-secondary"><br></p>
            <div class="row text-secondary mb-3">
                <div class="col">
                    <p class="h2 mb-0 text-info">{{ $user->totalPosts }}</p>Posts
                </div>
                <div class="col">
                    <p class="h2 mb-0 text-primary">{{ $user->totalVisits }}</p>Visits
                </div>
            </div>
            <p><a href="/users/{{ $user->username }}" class="btn btn-primary">View Profile</a></p>
        </div>
        @if ($user->latestLogin != null)
            <div class="card-footer text-muted"><small>Latest Visit
                    {{ $user->latestLogin->isoFormat('dddd DD MMMM YYYY [at] HH:mm') }}</small></div>
        @endif
    </div>
</div>

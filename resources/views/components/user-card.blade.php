@props(['user'])
<div class="col-12 col-md-4 mb-4">
    <div class="card text-center mb-3 bg-light h-100">
        <div class="card-header text-white {{$headingBackground($user->totalPosts)}}"><a href="/users/{{ $user->username }}" class="d-block {{$headingForeground($user->totalPosts)}}">
                <h3 class="card-title mb-0">{{ $user->username }}</h3>
            </a></div>
        <div class="card-body">
            <p class="card-subtitle">{{ $user->name }}</p>
            <p class="card-text text-secondary">
                @if($user->popname)
                    <q><em class="font-italic pl-1 pr-1">{{$user->popname}}</em></q>
                @endif
            </p>
            <div class="row text-secondary mb-3">
                <div class="col">
                    <p class="h2 mb-0 {{$classy($user->totalPosts)}}">{{ $user->totalPosts }}</p>Posts
                </div>
                <div class="col">
                    <p class="h2 mb-0 {{$classy($user->totalVisits)}}">{{ $user->totalVisits }}</p>Visits
                </div>
            </div>
            <p><a href="/users/{{ $user->username }}" class="btn btn-primary">View Profile</a></p>
        </div>
        @if ($user->latestLogin != null)
            <div class="card-footer text-muted"><small>Latest Visit
                    {{ $formatDate($user->latestLogin) }}</small></div>
        @endif
    </div>
</div>

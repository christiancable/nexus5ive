<div class="card text-white bg-info">
    <div class="card-header">
    Score Card
    </div>
    <ul class="list-group list-group-flush text-white bg-info">
        <li class="list-group-item text-white bg-info"><strong>Total Posts</strong> {{$user->totalPosts}}</li>
        <li class="list-group-item text-white bg-info"><strong>Total Visits</strong> {{$user->totalVisits}}</li>
        <li class="list-group-item text-white bg-info">
        @if ($user->latestLogin)
            <strong>Latest Visit</strong> {{$user->latestLogin->diffForHumans()}}
            @else
            <strong>Latest Visit</strong> Never
            @endif
        </li>
    </ul>
</div>
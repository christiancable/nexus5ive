<span wire:poll.{{ $pollingInterval }}s="fetchMentions">
    @if ($mentionsCount > 0)
        <ul class="nav navbar-nav ml-auto">
            <li class="dropdown nav-item">
                <a href="#" class="dropdown-toggle nav-link" data-toggle="dropdown" role="button" aria-haspopup="true"
                    aria-expanded="false" id="mentiondropdown" dusk='mentions-menu-toggle'>
                    <x-heroicon-s-bell-alert class="icon_mini mr-1" aria-hidden="true" />
                    <span class="badge  badge-danger" dusk='mentions-count'>{{ $mentionsCount }}</span>
                </a>

                <div class="dropdown-menu dropdown-menu-right" aria-labelledby="mentiondropdown">
                    @foreach ($mentions as $mention)
                        <a class="dropdown-item" href="{{ App\Helpers\TopicHelper::routeToPost($mention->post) }}">
                            <strong>{{ $mention->post->author->username }}</strong> mentioned you in
                            <strong>{{ $mention->post->topic->title }}</strong>
                        </a>
                    @endforeach
                    <div role="separator" class="dropdown-divider"></div>




                    <button type="submit" class="btn btn-link dropdown-item" id="Clear All Mentions"
                        dusk="mentions-clear" wire:click="clearMentions">
                        <x-heroicon-s-check class="icon_mini mr-1" aria-hidden="true" />Clear All
                        Mentions
                    </button>

                </div>
            </li>
        </ul>
    @endif
</span>

<span wire:poll.{{ $pollingInterval }}s="fetchMentions" x-data="{ open: false }">
    @if ($mentionsCount > 0)
        <ul class="nav navbar-nav ms-auto">
            <li class="dropdown nav-item position-static">
                <a href="#" 
                    class="nav-link dropdown-toggle" 
                    @click.prevent="open = !open"
                    @click.away="open = false"
                    role="button"
                    :aria-expanded="open"
                    id="mentiondropdown"
                    dusk='mentions-menu-toggle'>
                    <x-heroicon-s-bell-alert class="icon_mini me-1" aria-hidden="true" />
                    <span class="badge  text-bg-danger" dusk='mentions-count'>{{ $mentionsCount }}</span>
                </a>

                <div wire:ignore.self 
                    class="dropdown-menu dropdown-menu-end" 
                    :class="{ 'show d-block': open }"
                    
                    style="z-index: 1000;"
                    aria-labelledby="mentiondropdown">
                    @foreach ($mentions as $mention)
                        <a class="dropdown-item" href="{{ App\Helpers\TopicHelper::routeToPost($mention->post) }}">
                            <strong>{{ $mention->post->author->username }}</strong> mentioned you in
                            <strong>{{ $mention->post->topic->title }}</strong>
                        </a>
                    @endforeach
                    <div role="separator" class="dropdown-divider"></div>




                    <button type="submit" class="btn btn-link dropdown-item" id="Clear All Mentions"
                        dusk="mentions-clear" wire:click="clearMentions">
                        <x-heroicon-s-check class="icon_mini me-1" aria-hidden="true" />Clear All
                        Mentions
                    </button>

                </div>
            </li>
        </ul>
    @endif
</span>

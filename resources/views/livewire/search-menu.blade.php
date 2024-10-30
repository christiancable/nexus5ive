<div x-data="{ open: false }">
    <li class="dropdown nav-item">
        <a href="#" class="dropdown-toggle nav-link" role="button" aria-haspopup="true" aria-expanded="false"
            id="mentiondropdown" wire:click.prevent @click="open = !open">
            <x-heroicon-s-magnifying-glass class="icon_mini mr-1" aria-hidden="true" />Search
        </a>

        <div class="dropdown-menu show" x-show="open" aria-labelledby="mentiondropdown" @click.away="open = false">
            <div class="px-4 py-3">
                <div class="form-group">
                    <label class="sr-only" for="topicFilter">Search</label>
                    <input type="text" class="form-control" id="topicFilter"
                        placeholder="Search for a Section or Topic" wire:model.live="searchTerm"
                        wire:keydown.enter="performSearch">
                </div>
                <div class="form-check">
                    <input type="checkbox" class="form-check-input" id="showRecent" wire:model.live="showRecent"
                        @click.stop>
                    <label class="form-check-label" for="showRecent">Only recent topics</label>
                </div>
            </div>
            <div role="separator" class="dropdown-divider"></div>
            @if ($this->matchedLocations->isNotEmpty())
                @foreach ($this->matchedLocations as $item)
                    <a class="dropdown-item"
                        href="{{ $item['is_section'] ? '/section/' : '/topic/' }}{{ $item['id'] }}">
                        @if ($item['is_section'])
                            <span>
                                <x-heroicon-s-folder class="icon_mini text-primary" />
                                {{ $item['title'] ?: $item['id'] . ' untitled' }}
                            </span>
                        @else
                            <span class="ml-3">
                                <x-heroicon-s-chat-bubble-bottom-center-text class="icon_mini text-muted" />
                                {{ $item['title'] ?: $item['id'] . ' untitled' }}
                            </span>
                        @endif
                    </a>
                @endforeach
            @else
                <a href="/search/{{ $searchTerm }}" class="dropdown-item">
                    <x-heroicon-s-magnifying-glass class="icon_mini mr-1" aria-hidden="true" />
                    Search for
                    <em>{{ $searchTerm }}</em>
                </a>
            @endif
        </div>
    </li>
</div>

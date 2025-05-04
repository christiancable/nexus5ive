<ul class="nav navbar-nav" wire:poll.{{ $pollingInterval }}s="fetchNotifications" >
    <li class="dropdown nav-item" x-data="{ open: false }">

        <a href="#" class="dropdown-toggle nav-link" aria-haspopup="true" aria-expanded="false"
        wire:click.prevent @click="open = !open"
            id="profiledropdown">
            {{ $user->username }} &ndash; {{ $user->popname }}
            @if ($notificationCount)
                <span class="badge text-bg-danger">{{ $notificationCount }}</span>
            @endif
        </a>

        <div class="dropdown-menu show" x-show="open" aria-labelledby="profiledropdown" @click.away="open = false" dusk="profile-menu" x-cloak>
            <a class="dropdown-item"
                href="{{ action('App\Http\Controllers\Nexus\UserController@show', ['user' => $user->username]) }}">
                <x-heroicon-m-user class="icon_mini me-1" aria-hidden="true" />Profile
                @if ($commentsCount)
                    <span class="badge text-bg-info">{{ $commentsCount }}</span>
                @endif
            </a>

            <a class="dropdown-item" href="{{ action('App\Http\Controllers\Nexus\ChatController@index') }}">
                <x-heroicon-m-chat-bubble-left-right class="icon_mini me-1" aria-hidden="true" />Chat
                @if ($messagesCount)
                    <span class="badge text-bg-info" dusk="chat-notification-count">{{ $messagesCount }}</span>
                @endif
            </a>

            @if ($messagesCount)
                <span dusk="chat-notifications">
                @foreach ($unreadChats as $key => $chat)
                    <a class="ps-5 dropdown-item"
                        href="{{ action('App\Http\Controllers\Nexus\ChatController@index', ['user' => $chat->partner->username]) }}">
                        <x-heroicon-m-at-symbol class="text-danger me-1 icon_mini"
                            aria-hidden="true" />{{ $chat->partner->username }}
                    </a>
                @endforeach
                </span>
            @endif

            @if ($user->administrator)
                <div role="separator" class="dropdown-divider"></div>
                <div class="dropdown-header dropdown-item">Administrator Goodies</div>
                <a class="dropdown-item" href="{{ action('App\Http\Controllers\Nexus\ModeController@index') }}">
                    <x-heroicon-s-wrench class="icon_mini me-1" aria-hidden="true" />Default Theme
                </a>
                <a class="dropdown-item" href="{{ action('App\Http\Controllers\Nexus\ReportController@index') }}">
                    <x-heroicon-s-flag class="icon_mini me-1" aria-hidden="true" />Moderation
                </a>
            @endif

            @if ($sectionsCount)
                <div role="separator" class="dropdown-divider"></div>
                <div class="dropdown-header dropdown-item">Moderator Goodies</div>
                <a class="dropdown-item" href="{{ action('App\Http\Controllers\Nexus\RestoreController@index') }}">
                    <x-heroicon-s-archive-box class="icon_mini me-1" aria-hidden="true" />Your Archive
                </a>
            @endif


            <div role="separator" class="dropdown-divider"></div>
            <form class="d-flex align-items-center" action="{{ url('/logout') }}" method="POST">
                @csrf
                <button class="btn btn-link dropdown-item"><x-heroicon-s-arrow-left-start-on-rectangle
                        class="icon_mini me-1" aria-hidden="true" />Logout</button>
            </form>
        </div>
    </li>
</ul>

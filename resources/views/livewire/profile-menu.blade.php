<ul class="nav navbar-nav" wire:poll.{{ $pollingInterval }}s="fetchNotifications">
    <li class="dropdown nav-item">

        <a href="#" class="dropdown-toggle nav-link" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"
            id="profiledropdown">
            {{ $user->username }} &ndash; {{ $user->popname }}
            @if ($notificationCount)
                <span class="badge badge-danger">{{ $notificationCount }}</span>
            @endif
        </a>

        <div class="dropdown-menu" aria-labelledby="profiledropdown">
            <a class="dropdown-item"
                href="{{ action('App\Http\Controllers\Nexus\UserController@show', ['user' => $user->username]) }}">
                <x-heroicon-s-user class="icon_mini mr-1" aria-hidden="true" />Profile
                @if ($commentsCount)
                    <span class="badge badge-info">{{ $commentsCount }}</span>
                @endif
            </a>

            <a class="dropdown-item" href="{{ action('App\Http\Controllers\Nexus\ChatController@index') }}">
                <x-heroicon-s-chat-bubble-left-right class="icon_mini mr-1" aria-hidden="true" />Messages
                @if ($messagesCount)
                    <span class="badge badge-info">{{ $messagesCount }}</span>
                @endif
            </a>

            @if ($user->administrator)
                <div role="separator" class="dropdown-divider"></div>
                <div class="dropdown-header dropdown-item">Administrator Goodies</div>
                <a class="dropdown-item" href="{{ action('App\Http\Controllers\Nexus\ModeController@index') }}">
                    <x-heroicon-s-wrench class="icon_mini mr-1" aria-hidden="true" />BBS Settings
                </a>
            @endif

            @if ($sectionsCount)
                <div role="separator" class="dropdown-divider"></div>
                <div class="dropdown-header dropdown-item">Moderator Goodies</div>
                <a class="dropdown-item" href="{{ action('App\Http\Controllers\Nexus\RestoreController@index') }}">
                    <x-heroicon-s-archive-box class="icon_mini mr-1" aria-hidden="true" />Your Archive
                </a>
            @endif


            <div role="separator" class="dropdown-divider"></div>
            <form class="form-inline" action="{{ url('/logout') }}" method="POST">
                @csrf
                <button class="btn btn-link dropdown-item"><x-heroicon-s-arrow-left-start-on-rectangle
                        class="icon_mini mr-1" aria-hidden="true" />Logout</button>
            </form>
        </div>
    </li>
</ul>
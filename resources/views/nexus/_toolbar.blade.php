<?php
$authUser = App\Models\User::withCount('sections')->findOrfail(Auth::id());
$sectionsCount = $authUser->sections_count;

$commentsCount = $authUser->newCommentCount();
$messagesCount = $authUser->newMessageCount();

$profileNotificationCount = $commentsCount + $messagesCount;
$notificationCount = $profileNotificationCount;
?>
<div id="top-toolbar" class="border-bottom mb-3">
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary navbar-transparent">
        <div class="container">

            <a class="navbar-brand" {!! App\Helpers\GoogleAnalyticsHelper::onClickEvent('TopNavigation', 'Home') !!} href="/">
                <x-heroicon-s-home class="icon_mini" aria-hidden="true" />
            </a>

            <button type="button" class="navbar-toggler" data-toggle="collapse" data-target="#navbar"
                aria-expanded="true" aria-controls="navbar">
                <span class="navbar-toggler-icon"></span>
                @if ($notificationCount > 0)
                    <span class="badge badge-danger" id="notification-count">{{ $notificationCount }}</span>
                @else
                    <span class="sr-only" id="notification-count">0</span>
                @endif
            </button>


            <div id="navbar" class="navbar-collapse collapse" style="">

                <ul class="nav navbar-nav mr-auto">
                    <li class="nav-item">

                    <li class="nav-item">
                        <a {!! App\Helpers\GoogleAnalyticsHelper::onClickEvent('TopNavigation', 'Latest') !!}
                            href="{{ action('App\Http\Controllers\Nexus\SectionController@latest') }}"
                            class="nav-link mr-1">
                            <x-heroicon-s-bolt class="icon_mini mr-1" aria-hidden="true" />Latest</a>
                    </li>

                    <li class="nav-item">
                        <a {!! App\Helpers\GoogleAnalyticsHelper::onClickEvent('TopNavigation', 'Catch-Up') !!}
                            href="{{ action('App\Http\Controllers\Nexus\SectionController@leap') }}"
                            class="nav-link mr-1" dusk="toolbar-next">
                            <x-heroicon-s-arrow-right-circle class="icon_mini mr-1" aria-hidden="true" />Next</a>
                    </li>

                    <a {!! App\Helpers\GoogleAnalyticsHelper::onClickEvent('TopNavigation', 'Users') !!} href="{{ action('App\Http\Controllers\Nexus\UserController@index') }}"
                        class="nav-link">
                        <x-heroicon-s-users class="icon_mini mr-1" aria-hidden="true" />Users</a>
                    </li>

                    <li class="nav-item">
                        <a {!! App\Helpers\GoogleAnalyticsHelper::onClickEvent('TopNavigation', 'Whos Online') !!}
                            href="{{ action('App\Http\Controllers\Nexus\ActivityController@index') }}"
                            class="nav-link mr-1">
                            <x-heroicon-s-globe-europe-africa class="icon_mini mr-1" aria-hidden="true" />Who's
                            Online</a>
                    </li>


                    <span id="navigationApp" v-cloak><search-menu></search-menu></span>
                    <li class="nav-item replace-with-vue">
                        <a {!! App\Helpers\GoogleAnalyticsHelper::onClickEvent('TopNavigation', 'Search') !!}
                            href="{{ action('App\Http\Controllers\Nexus\SearchController@index') }}"
                            class="nav-link mr-1">
                            <x-heroicon-s-magnifying-glass class="icon_mini mr-1" aria-hidden="true" />Search</a>
                    </li>
                </ul>


                <livewire:mentions />
            
                <ul class="nav navbar-nav">
                    <li class="dropdown nav-item">

                        <a href="#" class="dropdown-toggle nav-link" data-toggle="dropdown" aria-haspopup="true"
                            aria-expanded="false" id="profiledropdown">
                            {{ $authUser->username }} &ndash; {{ $authUser->popname }}
                            @if ($profileNotificationCount)
                                <span class="badge badge-danger">{{ $profileNotificationCount }}</span>
                            @endif
                        </a>

                        <div class="dropdown-menu" aria-labelledby="profiledropdown">
                            <a class="dropdown-item"
                                href="{{ action('App\Http\Controllers\Nexus\UserController@show', ['user' => $authUser->username]) }}">
                                <x-heroicon-s-user class="icon_mini mr-1" aria-hidden="true" />Profile
                                @if ($commentsCount)
                                    <span class="badge badge-info">{{ $commentsCount }}</span>
                                @endif
                            </a>

                            <a class="dropdown-item"
                                href="{{ action('App\Http\Controllers\Nexus\ChatController@index') }}">
                                <x-heroicon-s-chat-bubble-left-right class="icon_mini mr-1"
                                    aria-hidden="true" />Messages
                                @if ($messagesCount)
                                    <span class="badge badge-info">{{ $messagesCount }}</span>
                                @endif
                            </a>

                            @if ($authUser->administrator)
                                <div role="separator" class="dropdown-divider"></div>
                                <div class="dropdown-header dropdown-item">Administrator Goodies</div>
                                <a class="dropdown-item"
                                    href="{{ action('App\Http\Controllers\Nexus\ModeController@index') }}">
                                    <x-heroicon-s-wrench class="icon_mini mr-1" aria-hidden="true" />BBS Settings
                                </a>
                            @endif

                            @if ($sectionsCount)
                                <div role="separator" class="dropdown-divider"></div>
                                <div class="dropdown-header dropdown-item">Moderator Goodies</div>
                                <a class="dropdown-item"
                                    href="{{ action('App\Http\Controllers\Nexus\RestoreController@index') }}">
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
            </div>
        </div>
    </nav>
</div>

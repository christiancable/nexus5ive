<div id="top-toolbar" class="border-bottom mb-3">
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary navbar-transparent">
        <div class="container">

            <a class="navbar-brand" {!! App\Helpers\GoogleAnalyticsHelper::onClickEvent('TopNavigation', 'Home') !!} href="/">
                <x-heroicon-s-home class="icon_mini" aria-hidden="true" />
            </a>

            <button type="button" class="navbar-toggler" data-toggle="collapse" data-target="#navbar"
                aria-expanded="true" aria-controls="navbar">
                <span class="navbar-toggler-icon"></span>
                @php
                    $notificationCount = auth()->user()->notificationCount();
                @endphp
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

                    <livewire:searchMenu />

                </ul>

                <livewire:mentions />
                <livewire:profileMenu />

            </div>
        </div>
    </nav>
</div>

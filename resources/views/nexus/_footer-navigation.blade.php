<footer id="footer-navigation" class="container">
    <nav class="fixed-bottom d-lg-none bg-primary">
        <div class="row no-gutters">

            <div class="text-center col">
                <a {!! App\Helpers\GoogleAnalyticsHelper::onClickEvent('BottomNavigation', 'Catch-Up') !!} href="{{ action('App\Http\Controllers\Nexus\SectionController@leap') }}"
                    class="text-white d-flex align-items-center justify-content-center py-3">
                    <x-heroicon-s-arrow-right-circle class="icon_medium mr-1" aria-hidden="true" />Next
                </a>
            </div>

            <div class="text-center col">
                <a {!! App\Helpers\GoogleAnalyticsHelper::onClickEvent('BottomNavigation', 'Latest') !!} href="{{ action('App\Http\Controllers\Nexus\SectionController@latest') }}"
                    class="text-white d-flex align-items-center justify-content-center py-3">
                    <x-heroicon-s-bolt class="icon_medium mr-1" aria-hidden="true" />Latest
                </a>
            </div>

        </div>
    </nav>
</footer>

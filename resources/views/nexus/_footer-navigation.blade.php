<footer id="footer-navigation" class="container">
    <nav class="fixed-bottom d-lg-none bg-primary">
        <div class="row no-gutters">

            <div class="text-center col">
                <a {!! App\Helpers\GoogleAnalyticsHelper::onClickEvent('BottomNavigation', 'Catch-Up') !!}
                href="{{ action('App\Http\Controllers\Nexus\SectionController@leap')}}" class="text-white d-block py-3">
                    <span class="h3 oi oi-arrow-circle-right mr-1" aria-hidden="true" style="vertical-align:middle"></span> Next
                </a>
            </div>

            <div class="text-center col">
                <a {!! App\Helpers\GoogleAnalyticsHelper::onClickEvent('BottomNavigation', 'Latest') !!}
                href="{{ action('App\Http\Controllers\Nexus\SectionController@latest')}}" class="text-white d-block py-3">
                    <span class="h3 oi oi-pulse mr-1" aria-hidden="true" style="vertical-align:middle"></span> Latest
                </a>
            </div>

        </div>
    </nav>
</footer>


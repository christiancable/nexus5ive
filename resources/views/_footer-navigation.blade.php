<footer id="footer-navigation" class="container">
    <nav class="fixed-bottom d-lg-none bg-info">
        <div class="row">

            <div class="text-center col">
                <a {!! App\Helpers\GoogleAnalyticsHelper::onClickEvent('BottomNavigation', 'Catch-Up') !!}
                href="{{ action('Nexus\SectionController@leap')}}" class="text-white">
                    <span class="oi oi-arrow-circle-right" aria-hidden="true" style="vertical-align:middle"></span> Next
                </a>
            </div>

            <div class="text-center col">
                <a {!! App\Helpers\GoogleAnalyticsHelper::onClickEvent('BottomNavigation', 'Latest') !!}
                href="{{ action('Nexus\SectionController@latest')}}" class="text-white">
                    <span class="oi oi-clock" aria-hidden="true" style="vertical-align:middle"></span> Latest
                </a>
            </div>

        </div>
    </nav>
</footer>


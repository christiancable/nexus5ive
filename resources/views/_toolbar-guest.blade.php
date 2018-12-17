<nav class="navbar navbar-expand-lg navbar-light navbar-laravel">
	<div class="container">
		<a class="navbar-brand" href="{{ url('/') }}">
			{{ config('app.name', 'Laravel') }}
		</a>

		<button type="button" class="navbar-toggler" data-toggle="collapse" data-target="#navbarSupportedContent" aria-expanded="true" aria-controls="navbar">
			<span class="navbar-toggler-icon"></span>
		</button>

		<div class="collapse navbar-collapse" id="navbarSupportedContent">
			<!-- Left Side Of Navbar -->
			<span class="navbar-nav mr-auto">
                <span class="navbar-text">
                   {{ config('nexus.subtitle') }}
                </span>
			</span>

			<!-- Right Side Of Navbar -->
            @if (config('nexus.allow_registrations'))
			<ul class="navbar-nav ml-auto">
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('register') }}">{{ __('Register') }}</a>
                </li>
            </ul>
            @endif 
	</div>
</nav>
<nav class="navbar navbar-expand-lg navbar-light">
	<div class="container">
		<a class="navbar-brand text-success" href="{{ url('/') }}">
			@if (config('nexus.logo_image'))
				<img src="{{config('nexus.logo_image')}}" alt="{{ config('app.name', 'Laravel') }}">
			@else 
				{{ config('app.name', 'Laravel') }}
			@endif
		</a>

		<button type="button" class="navbar-toggler" data-toggle="collapse" data-target="#navbarSupportedContent" aria-expanded="true" aria-controls="navbar">
			<span class="navbar-toggler-icon"></span>
		</button>

		<div class="collapse navbar-collapse" id="navbarSupportedContent">
			<!-- Left Side Of Navbar -->
			<span class="navbar-nav mr-auto d-none d-lg-block">
                <span class="navbar-text">
                   {{ config('nexus.subtitle') }}
                </span>
			</span>

			<!-- Right Side Of Navbar -->
			@if (!isset($hideRegistration))
				@if (config('nexus.allow_registrations'))
				<ul class="navbar-nav ml-auto">
					<li class="nav-item">
						<a class=" btn btn-primary" href="{{ route('register') }}"><span class="oi oi-aperture mr-2"></span>{{ __('Join') }}</a>
					</li>
				</ul>
				@endif 
            @endif 
	</div>
</nav>
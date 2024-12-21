<nav class="navbar navbar-expand-lg navbar-light">
	<div class="container">
		<a class="navbar-brand text-success" href="{{ url('/') }}">
			@if (config('nexus.logo_image'))
				<img src="{{asset(config('nexus.logo_image'))}}" alt="{{ config('nexus.name', 'Laravel') }}">
			@else 
				{{ config('nexus.name', 'Laravel') }}
			@endif
		</a>

		<button type="button" class="navbar-toggler" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-expanded="true" aria-controls="navbar">
			<span class="navbar-toggler-icon"></span>
		</button>

		<div class="collapse navbar-collapse" id="navbarSupportedContent">
			<!-- Left Side Of Navbar -->
			<span class="navbar-nav me-auto d-none d-lg-block">
                <span class="navbar-text">
                   {{ config('nexus.subtitle') }}
                </span>
			</span>

			<!-- Right Side Of Navbar -->
			@if (!isset($hideRegistration))
				@if (config('nexus.allow_registrations'))
				<ul class="navbar-nav ms-auto">
					<li class="nav-item">
						<a class=" btn btn-primary" href="{{ route('register') }}">
						 <x-heroicon-s-rocket-launch class="icon_mini me-1" aria-hidden="true" />
						{{ __('Join') }}</a>
					</li>
				</ul>
				@endif 
            @endif 
	</div>
</nav>
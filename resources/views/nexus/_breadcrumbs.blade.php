<div class="container">
	<nav aria-label="breadcrumb">
		<ol class="breadcrumb">
			@foreach ($breadcrumbs as $crumb)
				@if($crumb['route'])
					<li class="breadcrumb-item" ><a href="{{$crumb['route']}}">{{$crumb['title']}}</a></li>
				@else 
					<li class="breadcrumb-item active">{{$crumb['title']}}</li>
				@endif 
			@endforeach
		</ol>
	</nav>
</div>
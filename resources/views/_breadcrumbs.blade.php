 <div class="container">
 	<ol class="breadcrumb">
 		@foreach ($breadcrumbs as $crumb)
 		@if($crumb['route'])
 		<li><a href="{{$crumb['route']}}">{{$crumb['title']}}</a></li>
 		@else 
 		<li class="active">{{$crumb['title']}}</li>
 		@endif 
 		@endforeach
 	</ol>
 </div> 
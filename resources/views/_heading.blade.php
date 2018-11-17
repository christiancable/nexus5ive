<h1 class="display-4">{{$heading}}</h1>

@if(!empty($lead))
    <span class="lead">{!! App\Helpers\NxCodeHelper::nxDecode($lead) !!}</span>
@endif 

@if(!empty($introduction))
    {!! App\Helpers\NxCodeHelper::nxDecode($introduction) !!}
@endif 
<hr>
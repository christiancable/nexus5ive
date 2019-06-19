<h1 class="display-4">
    {{$heading}}
    @if(!empty($icon))
    <span class="h1 oi oi-{{$icon}} ml-1 d-none d-md-inline text-muted" aria-hidden="true" style="opacity: 0.2"></span>
    @endif 
</h1>

@if(!empty($lead))
    <span class="lead">
        {!! App\Helpers\NxCodeHelper::nxDecode($lead) !!}
    </span>
@endif 

@if(!empty($introduction))
    {!! App\Helpers\NxCodeHelper::nxDecode($introduction) !!}
@endif 
<hr>
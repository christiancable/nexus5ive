@props([
    'heading' => null,
    'lead' => null,
    'introduction' => null,
    'icon' => null,
])
<h1 class="display-4">
    {{ $heading }}
    @isset($icon)
    <span style="opacity: 0.2">{{ $icon }}</span>
    @endisset
</h1>

@isset($lead)
    <span class="lead">
        {!! App\Helpers\NxCodeHelper::nxDecode($lead) !!}
    </span>
@endisset

@isset($introduction)
    {!! App\Helpers\NxCodeHelper::nxDecode($introduction) !!}
@endisset

<hr>
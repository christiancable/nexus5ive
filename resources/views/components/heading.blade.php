<h1 class="display-4">
    {!! Str::inlineMarkdown($heading) !!}
    @isset($icon)
    <span style="opacity: 0.2">{{ $icon }}</span>
    @endisset
</h1>

@isset($lead)
    <span class="fs-6">
        {!! \App\Helpers\NxCodeHelper::nxDecode($lead) !!}
    </span>
@endisset

@isset($introduction)
    {!! App\Helpers\NxCodeHelper::nxDecode($introduction) !!}
@endisset

{{ $slot }}
<hr>

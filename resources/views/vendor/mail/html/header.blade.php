@props(['url'])
<tr>
<td class="header">
<a href="{{ $url }}" style="display: inline-block;">
@if (config('nexus.logo_image'))
<img src="{{asset(config('nexus.logo_image'))}}" alt="{{ config('nexus.name', 'Laravel') }}">
@else
{{ $slot }}
@endif
</a>
</td>
</tr>

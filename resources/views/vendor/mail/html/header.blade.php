@props(['url'])
<tr>
<td class="header">
<a href="{{ $url }}" style="display: inline-block;">
@if (trim($slot) === 'SenvaTec')
<img src="{{asset('images/logo_artguz_clima.png')}}" class="logo" alt="SenvaTec">
@else
{{ $slot }}
@endif
</a>
</td>
</tr>

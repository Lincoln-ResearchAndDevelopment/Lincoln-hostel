@props(['url'])
<tr>
<td class="header">
<a href="{{ $url }}" style="display: inline-block;">
@if (trim($slot) === 'Laravel' || trim($slot) === 'Lincoln Hostel')
<img src="{{ asset('assets/img/lincoln-logo.png') }}" class="logo" alt="Lincoln Hostel Logo" style="max-height: 50px; width: auto;">
@else
{{ $slot }}
@endif
</a>
</td>
</tr>

 <tr>
  <td><a href="{{action('App\Http\Controllers\Nexus\ChatController@conversation', ['username' => $activity->user->username])}}">
    <x-heroicon-s-chat-bubble-left-right class="icon_mini mr-1" aria-hidden="true" />
</a></td>
<td>{!! $activity->user->present()->profileLink !!}</td>
    <td class="d-none d-sm-table-cell">{{$activity->user->popname}}</td>
    <td><a href="{{$activity->route}}">{!! $activity->text !!}</a></td>
    <td class="text-muted">{{$activity->time->diffForHumans()}} </td>
</tr>

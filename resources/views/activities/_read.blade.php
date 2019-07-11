 <tr>
  <td><a href="{{action('Nexus\ChatController@conversation', ['username' => $activity->user->username])}}">
    <span class="oi oi-chat" aria-hidden="true"></span>
</a></td>
<td>{!! $activity->user->present()->profileLink !!}</td>
    <td class="d-none d-sm-table-cell">{{$activity->user->popname}}</td>
    <td><a href="{{$activity->route}}">{!! $activity->text !!}</a></td>
    <td class="text-muted">{{$activity->time->diffForHumans()}} </td>
</tr>

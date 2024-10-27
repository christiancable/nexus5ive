 <tr>
     <td>
        @if(Auth::id() != $activity->user->id)
        <a
             href="{{ action('App\Http\Controllers\Nexus\ChatController@index', ['user' => $activity->user->username]) }}">
             <x-heroicon-s-chat-bubble-left-right class="icon_mini" aria-hidden="true" />
         </a>
         @endif
         </td>
     <td>{!! $activity->user->present()->profileLink !!}</td>
     <td class="d-none d-sm-table-cell">{{ $activity->user->popname }}</td>
     <td><a href="{{ $activity->route }}">{!! $activity->text !!}</a></td>
     <td class="text-muted">{{ $activity->time->diffForHumans() }} </td>
 </tr>

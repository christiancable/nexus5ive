<tr>
    <td>
        <strong><x-profile-link :user="$comment->author" /></strong>
    </td>
    <td class="break-long-words comment">    
        @if ($comment->read === true)
        {{$comment->text}}
        @else 
        <strong>{{$comment->text}}</strong>
        @endif 
    </td>
    <td>
        <form action="{{action('App\Http\Controllers\Nexus\CommentController@destroy', ['comment' => $comment->id])}}" 
            method="POST"
            class="d-flex flex-row-reverse"
            >
        @csrf
        @method('DELETE')
            <x-ui.button variant="danger" type="submit" class="position-end">
                <x-heroicon-s-trash class="icon_mini mr-1" aria-hidden="true" />Delete
            </x-ui.button>
        </form>
    </td>
</tr>
<tr>
    <td>
        <strong>{!! $comment->author->present()->profileLink !!}</strong>
    </td>
    <td class="break-long-words">    
        @if ($comment->read === true)
        {{$comment->text}}
        @else 
        <strong>{{$comment->text}}</strong>
        @endif 
    </td>
    <td>
        <form action="{{action('App\Http\Controllers\Nexus\CommentController@destroy', ['comment' => $comment->id])}}" method="POST">
        @csrf
        @method('DELETE')
            <button class="btn btn-danger"><span class="oi oi-trash" aria-hidden="true"></span> Delete </button>
        </form>
    </td>
</tr>
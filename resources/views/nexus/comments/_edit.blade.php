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
            <button class="btn btn-danger">
            <x-heroicon-s-trash class="icon_mini mr-1" aria-hidden="true" />Delete</button>
        </form>
    </td>
</tr>
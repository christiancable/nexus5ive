<tr>
    <td class="col-1"><strong>
       {!! $comment->author->present()->profileLink !!}
    </strong>
</td>
<td class="col break-long-words">    
    @if ($comment->read === true)
    {{$comment->text}}
    @else 
    <strong>{{$comment->text}}</strong>
    @endif 
</td>
<td class="col-1">
    <form action="{{action('Nexus\CommentController@destroy', ['id' => $comment->id])}}" method="POST">
    {{ csrf_field() }}
    {{ method_field('DELETE') }}
        <button class="btn btn-danger"><span class="oi oi-trash aria-hidden="true"></span> Delete </button>
    </form>
</td>
</tr>
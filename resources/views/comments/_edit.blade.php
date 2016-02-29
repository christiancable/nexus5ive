<tr>
    <td class="col-sm-1"><strong>
        <a href="{{ action('Nexus\UserController@show', ['user_name' => $comment->author->username]) }}">{{$comment->author->username}}
        </a>
    </strong>
</td>
<td class="col-sm-10 break-long-words">    
    @if ($comment->read === true)
    {{$comment->text}}
    @else 
    <strong>{{$comment->text}}</strong>
    @endif 
</td>
<td class="col-sm-1">
    <form action="{{action('Nexus\CommentController@destroy', ['id' => $comment->id])}}" method="POST">
    {{ csrf_field() }}
    {{ method_field('DELETE') }}
        <button class="btn btn-default btn-xs danger"><span class="danger glyphicon glyphicon-trash" aria-hidden="true"></span> Delete </button>
    </form>
</td>
</tr>
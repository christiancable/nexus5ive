<tr>
    <td class="col-sm-2"><strong>
        <a href="{{ action('Nexus\UserController@show', ['user_name' => $comment->author->username]) }}">{{$comment->author->username}}
        </a>
    </strong>
</td>
<td class="col-sm-10 break-long-words">    
    {{$comment->text}}
</td>
</tr>
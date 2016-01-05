<tr>
    <td class="col-sm-1"><strong>
        <a href="{{ action('Nexus\UserController@show', ['user_name' => $comment->author->username]) }}">{{$comment->author->username}}
        </a>
    </strong>
</td>
<td class="col-sm-10">    
    {{$comment->text}}
</td>
</tr>
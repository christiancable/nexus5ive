<tr>
    <td class="col-sm-2"><strong>
        {!! $comment->author->present()->profileLink !!}
    </strong>
</td>
<td class="col-sm-10 break-long-words">    
    {{$comment->text}}
</td>
</tr>
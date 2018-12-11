<tr>
    <td class="col-2"><strong>
        {!! $comment->author->present()->profileLink !!}
    </strong>
</td>
<td class="col break-long-words">    
    {{$comment->text}}
</td>
</tr>
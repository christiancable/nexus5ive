<tr>
    <td><strong>
        {!! $comment->author->present()->profileLink !!}
    </strong>
</td>
<td class="break-long-words">    
    {{$comment->text}}
</td>
</tr>
<tr>
    <td><strong>
        <x-profile-link :user="$comment->author" />
    </strong>
</td>
<td class="break-long-words comment">    
    {{$comment->text}}
</td>
</tr>
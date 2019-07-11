<h3>👋</h3>
<p>
Start a conversation.
</p>

@php
$users = App\User::select('username')->verified()->orderBy('username')->get();
@endphp

<div class="dropdown d-inline">
    <button class="btn btn-secondary dropdown-toggle" type="button" id="sectionMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
        Chat to…
    </button>
    <div class="dropdown-menu" aria-labelledby="sectionMenuButton">
        @foreach ($users as $user)        
        <a class="dropdown-item" href="{{ action('Nexus\ChatController@conversation', ['username' => $user['username'] ] )  }}">{{$user['username']}}</a>
        @endforeach        
    </div>
</div>

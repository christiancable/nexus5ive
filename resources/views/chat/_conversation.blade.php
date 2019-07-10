<div>
    @foreach ($conversation as $message)        
    
    @if (Auth::user()->id === $message->author->id)
    @php $mine = true @endphp
    @else 
    @php $mine = false @endphp
    @endif 
    
    <div>
        @if ($loop->first)
            @include('chat._name', ['username' => $message->author->username])    
            @php 
            $previousMessageAuthorId = $message->author->id;
            @endphp       
        @else
            @if ($previousMessageAuthorId != $message->author->id)
                @include('chat._name', ['username' => $message->author->username, $mine])
            @endif 
            @php 
            $previousMessageAuthorId = $message->author->id;
            @endphp       
        @endif
        
        @include('chat._message', [$message, $mine])
    </div>
    
    @endforeach
</div>
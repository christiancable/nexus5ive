    {{-- add new post section --}}
    @if($readonly === true) 
        <div class="alert alert-danger" role="alert">
            <p>{!!__('nexus.topic.closed')!!}</p>
        </div>
    @else 
        {{-- only show the post box if we are on the first page --}}
        @if ($postsChunk->currentPage() === 1)
        	  @include('nexus.posts.create', compact('topic', 'replyingTo'))
        @endif
    @endif 
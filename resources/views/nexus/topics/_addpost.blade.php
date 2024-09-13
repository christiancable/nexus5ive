    {{-- add new post section --}}
    @if($readonly === true) 
        <div class="alert alert-danger" role="alert">
            <p><strong>This topic is closed</strong>. You cannot make a new comment.</p>
        </div>
    @else 
        {{-- only show the post box if we are on the first page --}}
        @if ($postsChunk->currentPage() === 1)
        	  @include('posts.create', compact('topic', 'replyingTo'))
        @endif
    @endif 
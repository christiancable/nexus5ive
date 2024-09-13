<form action="{{ route('topic.updateSubscription', ['topic' => $topic->id]) }}" method="POST" class="form">
    @csrf
    <button class="btn btn-link">
        @if($unsubscribed)
            <span class="oi oi-check text-success" aria-hidden="true"></span><span> Subscribe to this topic</span>
            <input type="hidden" name="command" value="subscribe">
        @else
            <span class="oi oi-x text-danger" aria-hidden="true"></span><span> Unsubscribe from this topic</span>
            <input type="hidden" name="command" value="unsubscribe">
        @endif
    </button>
</form>

<form action="{{ route('topic.updateSubscription', ['topic' => $topic->id]) }}" method="POST" class="form">
    @csrf
    <button class="btn btn-link">
        @if ($unsubscribed)
            <x-heroicon-s-check class="icon_mini mr-1 text-success" aria-hidden="true" />
            <span>Subscribe to this topic</span>
            <input type="hidden" name="command" value="subscribe">
        @else
            <x-heroicon-s-x-mark class="icon_mini mr-1 text-danger" aria-hidden="true" />
            <span>Unsubscribe from this topic</span>
            <input type="hidden" name="command" value="unsubscribe">
        @endif
    </button>
</form>

<nav class="list-group">
    @foreach ($conversationPartners as $partner)
    <a class="list-group-item list-group-item-action {{ $partner['username'] === $currentPartner ? 'active' : ''}}" 
        href="/chat/{{ $partner['username'] }}">{{ $partner['username'] }} 
        @if ($partner['unread'] && $partner['username'] != $currentPartner) 
        <span class="badge badge-success ml-1">{{ $partner['unread'] }}</span>
        @endif
    </a>
    @endforeach
</nav>
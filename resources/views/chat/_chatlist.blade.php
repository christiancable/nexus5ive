<nav class="list-group">
    @foreach ($conversationPartners as $partner)
    <a class="list-group-item list-group-item-action {{ $partner === $currentPartner ? 'active' : ''}}" 
        href="/chat/{{ $partner }}">{{ $partner }}
    </a>
    @endforeach
</nav>
<a href="{{ $link }}">
    <h2 class="card-title">
        @switch($icon)
            @case('unsubscribed')
                <x-heroicon-s-hand-thumb-down class="icon_topic {{ $textClass }} " />
            @break

            @case('new_posts')
                <x-heroicon-s-fire class="icon_topic {{ $textClass }}" />
            @break

            @case('never_read')
                <x-heroicon-s-star class="icon_topic {{ $textClass }}" />
            @break

            @default
                <x-heroicon-s-chat-bubble-bottom-center-text class="icon_topic {{ $textClass }}" />
        @endswitch
        {{ $title }}
    </h2>
</a>

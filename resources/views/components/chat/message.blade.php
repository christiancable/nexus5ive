@props(['message'])

<div class="d-flex mb-4 {{ $message['author_id'] == auth()->id() ? 'justify-content-end' : 'justify-content-start' }}">
    <div class="msg_cotainer {{ $message['author_id'] == auth()->id() ? 'msg_cotainer_send' : '' }}">
        {{ $message['text'] }}
        <span class="msg_time">{{ \Carbon\Carbon::parse($message['time'])->format('g:i A') }}</span>
    </div>
</div>
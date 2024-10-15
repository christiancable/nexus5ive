@props(['message'])

<div class="d-flex mb-4 {{ $message['author_id'] == auth()->id() ? 'justify-content-end' : 'justify-content-start' }}" title="{{ \Carbon\Carbon::parse($message['time'])->diffForHumans() }}">

    <div class="px-3 rounded-pill msg_cotainer {{ $message['author_id'] == auth()->id() ? 'msg_cotainer_send' : '' }}">
        <p>{!! App\Helpers\NxCodeHelper::nxDecode($message['text']) !!}</p>
    </div>

    <span class="msg_time">{{ \Carbon\Carbon::parse($message['time'])->diffForHumans() }}</span>


</div>

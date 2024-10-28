@props(['message'])

<div class="d-flex mb-4 {{ $message['sender_id'] == auth()->id() ? 'justify-content-end' : 'justify-content-start' }}"
    title="{{ \Carbon\Carbon::parse($message['created_at'])->diffForHumans() }}">

    <div class="px-3 py-2 {{ $message['sender_id'] == auth()->id() ? 'msg_author ' : 'msg_receiver' }} mb-n1">
        {!! Str::chopEnd(Str::chopStart(App\Helpers\NxCodeHelper::nxDecode($message['message_text']), '<p>'),'</p>') !!}
    </div>
</div>


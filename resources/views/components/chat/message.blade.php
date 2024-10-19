@props(['message'])

<div class="d-flex mb-4 {{ $message['author_id'] == auth()->id() ? 'justify-content-end' : 'justify-content-start' }}"
    title="{{ \Carbon\Carbon::parse($message['time'])->diffForHumans() }}">

    <div class="px-3 py-2 {{ $message['author_id'] == auth()->id() ? 'msg_author ' : 'msg_receiver' }} mb-n1">
        {!! Str::chopEnd(Str::chopStart(App\Helpers\NxCodeHelper::nxDecode($message['text']), '<p>'),'</p>') !!}
    </div>
</div>


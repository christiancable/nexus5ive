<div 
    class="bg-transparent text-black {{ $mine ? 'border-primary' : 'border-success'}}  py-2 px-3 mb-3 d-flex justify-content-between border-left">
  <span>{!!$message->text!!}</span>
  <small class="text-muted mx-3 d-none d-md-inline">{{$message->time->diffForHumans()}}</small>
</div>
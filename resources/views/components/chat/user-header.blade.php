  @props(['user', 'latest_message'])
  <div class="card-header msg_head">
      <div class="d-flex bd-highlight justify-content-between">
              <p>{{ $user->username }}<p>
              @if($latest_message)
              <small>{{ $latest_message->time->format('D, j M Y \a\t H:i') }}</small>
              @endif
      </div>
  </div>
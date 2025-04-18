  @props(['user', 'latest_message', 'users'])
  <div class="card-header msg_head">
      <div class="d-flex bd-highlight justify-content-between">
          <div>
              <label class="visually-hidden">To:</label>
              <select class="form-select user-select" id="usersHeaderDropdown" wire:model="newChatUser"
                  wire:change="selectUser($event.target.value)">
                  @foreach ($users as $user)
                      <option value="{{ $user->id }}">{{ $user->username }}</option>
                  @endforeach
              </select>
          </div>
          <div>
              @if ($latest_message)
                  <small>{{ $latest_message->created_at->format('D, j M Y \a\t H:i') }}</small>
              @endif
          </div>
      </div>
  </div>

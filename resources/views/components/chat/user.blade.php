  @props(['user', 'chat_id', 'active', 'is_read'])
  <li class="list-group-item {{ $active ? 'bg-primary text-white' : '' }}" wire:click="selectChat({{ $chat_id }})">
      <span class="d-flex justify-content-between align-items-center">
          {{ $user['username'] }}
          @if (!$is_read)
              <x-heroicon-s-sparkles class="icon_mini text-warning" />
          @endif
      </span>
  </li>

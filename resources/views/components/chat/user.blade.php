  @props(['user', 'active'])
  <li class="list-group-item {{ $active ? 'bg-primary text-white' : '' }}" wire:click="selectUser({{ $user['id'] }})">
      <span>{{ $user['username'] }}</span>
      <p>{{ $user['name'] }}</p>
  </li>

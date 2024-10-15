  @props(['user', 'active'])

  <li class="{{ $active ? 'active' : '' }}"
      wire:click="selectUser({{ $user['id'] }})">
      <div class="d-flex bd-highlight">
          <div class="user_info">
              <span>{{ $user['username'] }}</span>
              <p>{{ $user['name'] }} is online</p>
          </div>
      </div>
  </li>

  @props(['user', 'active'])

  <li class="{{ $active ? 'active' : '' }}"
      wire:click="selectUser({{ $user['id'] }})">
      <div class="d-flex bd-highlight">
          <div class="img_cont">
              <img src="https://via.placeholder.com/50" class="rounded-circle user_img">
              <span class="online_icon"></span>
          </div>
          <div class="user_info">
              <span>{{ $user['username'] }}</span>
              <p>{{ $user['name'] }} is online</p>
          </div>
      </div>
  </li>

<div>

    <div role="search" class="mb-3">
        <input wire:model.live="search" placeholder="Search for a user" autofocus="autofocus" class="form-control"
            dusk="user-filter">
    </div>

    @if (count($this->users) > 0)
        <div class="row" dusk="user-grid">
            @foreach ($this->users as $key => $user)
                <x-user-card :user="$user" />
            @endforeach
        </div>
    @endif

    @if (count($this->users) === 0)
        <div class="alert alert-info" dusk="no-users-found" role="alert">
            <p>
                No users found for <strong>{{ $search }}</strong>
            </p>
        </div>
    @endif
</div>

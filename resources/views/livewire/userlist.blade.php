<div>

    <div role="search" class="form-group">
        <input wire:model.live="search" placeholder="Search for a user" autofocus="autofocus" class="form-control"
            dusk="user-filter">
    </div>

    {{-- <div class="card-deck"> --}}
    @foreach ($this->users as $key => $user)
        <x-user-card :user="$user" />
    @endforeach


    @if(count($this->users) === 0)
        <div class="alert alert-info" role="alert">
            <p>
                No users found found for <strong>{{ $search }}</strong>
            </p>
        </div>
    @endif
    {{-- </div> --}}
</div>

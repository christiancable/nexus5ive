<div>
    <p>this is where the userlist will appear</p>
    <input wire:model.live="search">
 
     @foreach ($this->users as $user)
        <div>{{ $user->name }}</div>
    @endforeach


    @foreach($users as $user)
        <li>{{ $user->username}}</li>
    @endforeach
    {{-- To attain knowledge, add things every day; To attain wisdom, subtract things every day. --}}
</div>

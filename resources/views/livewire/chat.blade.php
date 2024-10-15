<div class="container-fluid h-100">
    <div class="row h-100">
        <div class="col-md-4 col-xl-3 chat-sidebar">
            <div class="card mb-sm-3 mb-md-0 contacts_card">
                <div class="card-header">
                    <h5 class="mb-0">Users</h5>
                </div>
                <div class="card-body contacts_body">
                    <ul class="contacts list-group list-group-flush">
                        @foreach($users as $user)
                            <x-chat.user :user="$user" :active="$selectedUser && $selectedUser->id == $user->id" />
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
        <div class="col-md-8 col-xl-9 chat">
            <div class="card h-100">
                @if($selectedUser)
                    <div class="card-header msg_head">
                        <div class="d-flex bd-highlight">
                            <div class="img_cont">
                                <img src="https://via.placeholder.com/50" class="rounded-circle user_img">
                                <span class="online_icon"></span>
                            </div>
                            <div class="user_info">
                                <span>Chat with {{ $selectedUser->name }}</span>
                                <p>{{ count($messages) }} Messages</p>
                            </div>
                        </div>
                    </div>
                    <div class="card-body msg_card_body">
                        @foreach($messages as $message)
                            <x-chat.message :message="$message" />
                        @endforeach
                    </div>
                    <div class="card-footer">
                        <div class="input-group">
                            <input type="text" wire:model="newMessage" wire:keydown.enter="sendMessage"
                                   class="form-control type_msg" placeholder="Type your message...">
                            <div class="input-group-append">
                                <button wire:click="sendMessage" class="btn btn-primary send_btn" type="button">
                                <x-heroicon-s-paper-airplane class="icon_mini" />
                                </button>
                            </div>
                        </div>
                    </div>
                @else
                    <div class="card-body msg_card_body d-flex justify-content-center align-items-center">
                        <p class="text-muted">Select a user to start chatting</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
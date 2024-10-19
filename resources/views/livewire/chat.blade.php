<div class="container" id="chat-container">
    <div class="row h-100">

        <div class="col-md-4 col-xl-3 h-100">

            <div class="card mb-sm-3 mb-md-0 h-100">
                <div class="card-header">
                    <h5 class="mb-0">Users</h5>
                </div>
                <div class="card-body chat_user_list_container p-0">
                    <ul class="list-group list-group-flush chat_user_list overflow-auto h-100">
                        @foreach ($users as $user)
                            <x-chat.user :user="$user" :active="$selectedUser && $selectedUser->id == $user->id" />
                        @endforeach
                    </ul>
                </div>
            </div>

        </div>


        <div class="col-md-8 col-xl-9 h-100">

            <div class="card d-flex flex-column h-100" wire:poll.{{ $pollingInterval }}s="loadMessages">
                @if ($selectedUser)

                    <x-chat.user-header :user="$selectedUser" :latest_message="$messages->last()"/>
                    
                    <div class="card-body p-0">
                        <div class="pr-2 pl-2 overflow-auto h-100 d-flex flex-column-reverse">
                            @foreach ($messages->reverse() as $message)
                                <x-chat.message :message="$message" />
                            @endforeach
                        </div>
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
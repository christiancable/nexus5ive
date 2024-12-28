<div class="container" id="chat-container">
    <div class="row h-100">

        <div class="col-md-4 col-xl-3 h-100 d-none d-md-block">

            <div class="card mb-sm-3 mb-md-0 h-100">
                <div class="card-body chat_user_list_container p-0 h-100">
                    <ul class="list-group list-group-flush chat_user_list overflow-auto h-100" dusk="chat-list">
                        @foreach ($chats as $chat)
                            <x-chat.user :user="$chat->partner" :chat_id="$chat->id" :active="$selectedChat && $selectedChat->id == $chat->id" :is_read="$chat->is_read" />
                        @endforeach
                    </ul>
                </div>
            </div>

        </div>


        <div class="col-md-8 col-xl-9 h-100">

            <div class="card d-flex flex-column h-100" wire:poll.{{ $pollingInterval }}s="loadMessages">
                @if ($selectedUser)

                    <x-chat.user-header :user="$selectedUser" :latest_message="$messages->last()" :users="$users" />



                    <div class="card-body p-0 h-75">
                        <div class="pe-2 ps-2 overflow-auto h-100 d-flex flex-column-reverse" dusk="chat-messages">
                            @foreach ($messages->reverse() as $message)
                                <x-chat.message :message="$message" />
                            @endforeach
                        </div>
                    </div>

                    <div class="card-footer flex-shrink-1">
                        <div class="input-group">
                            <input type="text" wire:model="newMessage" wire:keydown.enter="sendMessage"
                                dusk="chat-input" class="form-control type_msg" placeholder="Type your message...">
                            <div class="input-group-append">
                                <button wire:click="sendMessage" class="btn btn-primary send_btn" type="button"
                                    dusk="chat-send-button">
                                    <x-heroicon-s-paper-airplane class="icon_mini" />
                                </button>
                            </div>
                        </div>
                    </div>
                @else
                    <div class="card-body msg_card_body d-flex justify-content-center align-items-center h-100">
                        <div class="text-center">
                            <h1>👋</h1>
                            <label class="visually-hidden" for="usersDropdown">Chat to&hellip;</label>
                            <select class="form-select" id="usersDropdown" wire:model="newChatUser"
                                wire:change="selectUser($event.target.value)">
                                <option value="null" disabled>Chat with...</option>
                                @foreach ($users as $user)
                                    <option value="{{ $user->id }}">{{ $user->username }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                @endif
            </div>

        </div>
    </div>
</div>

<div class="container">
    <style>
        {{-- 
        .scroll_list_container {
            height: 70vh;
            overflow: hidden;
        }

        .scroll_list {
            height: 100%;
            overflow-y: auto;
        }


        .chat_user_list {
            height: 100%;
            overflow-y: auto;
        }

        .msg_list {
            height: 100%;
            overflow-y: auto;
        } 
        --}} .msg_author {
            background-color: #28a745;
            color: #fff;
            border-radius: 50px;
            border-bottom-right-radius: 0;
        }

        .msg_receiver {
            background-color: #007bff;
            color: #fff;
            border-radius: 50px;
            border-bottom-left-radius: 0;
        }
    </style>
    <div class="row">

        <div class="col-md-4 col-xl-3">

            <div class="card mb-sm-3 mb-md-0" style="height:70vh;">
                <div class="card-header">
                    <h5 class="mb-0">Users</h5>
                </div>
                <div class="card-body chat_user_list_container">
                    <ul class="contacts list-group list-group-flush chat_user_list" style="overflow-y:auto; height:100%">
                        @foreach ($users as $user)
                            <x-chat.user :user="$user" :active="$selectedUser && $selectedUser->id == $user->id" />
                        @endforeach
                    </ul>
                </div>
            </div>

        </div>


        <div class="col-md-8 col-xl-9">

            <div class="card d-flex flex-column" style="height:70vh;">
                @if ($selectedUser)

                    <div class="card-header msg_head">
                        <div class="d-flex bd-highlight">
                            <div>
                                <span>Chat with {{ $selectedUser->name }}</span>
                                <p>{{ count($messages) }} Messages</p>
                            </div>
                        </div>
                    </div>

                    <div class="card-body" style="overflow:hidden;">
                        <div class="pr-2" style="overflow-y:auto; height:100%">
                            @foreach ($messages as $message)
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

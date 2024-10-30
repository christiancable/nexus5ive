<?php

namespace App\Livewire;

use App\Helpers\ChatHelper;
use App\Models\Chat as ChatModel;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Chat extends Component
{
    public $users;

    public $user;

    public $messages;

    public $newMessage;

    public $chats;

    public $selectedUser = null;

    public $selectedChat = null;

    public $pollingInterval;

    public $newChatUser;

    public function mount(Request $request)
    {
        $this->pollingInterval = 1;
        $this->users = User::where('id', '!=', Auth::id())->orderBy('username')->get();
        $this->messages = collect();
        $this->user = $request->user();
        $this->loadMessages();
    }

    /*
     load a chat by selecting the user from a dropdown or url action
    */
    public function selectUser($userId)
    {
        $this->selectedUser = User::find($userId);
        // find or create a chat of this user
        $this->selectedChat = ChatModel::where([
            'owner_id' => Auth::id(),
            'partner_id' => $this->selectedUser->id
        ])->first();
        $this->loadMessages();
    }

    /*
    load a chat by choosing from existing chat list
    */
    public function selectChat($chatId)
    {
        $this->selectedChat = ChatModel::find($chatId);
        $this->selectedUser = $this->selectedChat->partner;
        $this->loadMessages();
    }

    public function loadMessages()
    {
        $this->chats = $this->user->chats;

        if (!$this->selectedChat && $this->selectedUser) {
            $this->selectedChat = ChatModel::where([
                'owner_id' => Auth::id(),
                'partner_id' => $this->selectedUser->id
            ])->first();
        }


        if ($this->selectedChat) {
            $this->messages = $this->selectedChat->chatMessages;
            if ($this->selectedChat->is_read == false) {
                $this->selectedChat->is_read = true;
                $this->selectedChat->save();
            }
        } else {
            $this->messages = collect();
        }

        if ($this->selectedUser) {
            $this->newChatUser = $this->selectedUser->id;
        }
    }

    public function sendMessage()
    {
        if ($this->selectedUser && $this->newMessage) {
            ChatHelper::sendMessage(Auth::id(), $this->selectedUser->id, $this->newMessage);
            $this->newMessage = '';
            $this->loadMessages();
        }
    }

    public function render()
    {
        return view('livewire.chat');
    }
}

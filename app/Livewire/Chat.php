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

    public function selectUser($userId)
    {
        $this->selectedUser = User::find($userId);
        $this->loadMessages();
    }

    public function loadMessages()
    {
        $this->chats = $this->user->chats;
        if ($this->selectedUser) {
            // find chat
            // @todo when this is a chosen from a list of chat
            // $chat = Chat::find($this->selectedChat);
            $chat = ChatModel::where([
                'owner_id' => Auth::id(),
                'partner_id' => $this->selectedUser->id
            ])->first();

            if ($chat) {
                $this->messages = $chat->chatMessages;
                if ($chat->is_read == false) {
                    $chat->is_read = true;
                    $chat->save();
                }
            } else {
                $this->messages = collect();
            }

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

<?php

namespace App\Livewire;

use App\Helpers\ChatHelper;
use App\Models\Chat as ChatModel;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Livewire\Component;

class Chat extends Component
{
    public Collection $users;

    public ?User $user = null;

    public Collection $messages;

    public ?string $newMessage = null;

    public Collection $chats;

    public ?User $selectedUser = null;

    public ?ChatModel $selectedChat = null;

    public int $pollingInterval = 0;

    public ?int $newChatUser = null;

    public function mount(Request $request): void
    {
        $this->pollingInterval = config('nexus.chat_poll_interval');
        $this->users = User::where('id', '!=', Auth::id())->verified()->orderBy('username')->get();
        $this->messages = new Collection;
        $this->chats = new Collection;
        $this->user = $request->user();
        $this->loadMessages();
    }

    /*
     load a chat by selecting the user from a dropdown or url action
    */
    public function selectUser(int $userId): void
    {
        $this->selectedUser = User::find($userId);
        // find or create a chat of this user
        $this->selectedChat = ChatModel::where([
            'owner_id' => Auth::id(),
            'partner_id' => $this->selectedUser->id,
        ])->first();
        $this->loadMessages();
    }

    /*
    load a chat by choosing from existing chat list
    */
    public function selectChat(int $chatId): void
    {
        $this->selectedChat = ChatModel::find($chatId);
        $this->selectedUser = $this->selectedChat->partner;
        $this->loadMessages();
    }

    public function loadMessages(): void
    {
        $this->chats = $this->user->chats;

        if (! $this->selectedChat && $this->selectedUser) {
            $this->selectedChat = ChatModel::where([
                'owner_id' => Auth::id(),
                'partner_id' => $this->selectedUser->id,
            ])->first();
        }

        if ($this->selectedChat) {
            $this->messages = $this->selectedChat->chatMessages;
            if ($this->selectedChat->is_read == false) {
                $this->selectedChat->is_read = true;
                $this->selectedChat->save();
            }
        } else {
            $this->messages = new Collection;
        }

        if ($this->selectedUser) {
            $this->newChatUser = $this->selectedUser->id;
        }
    }

    public function sendMessage(): void
    {
        $this->authorize('create', ChatModel::class);

        if ($this->selectedUser && $this->newMessage) {
            ChatHelper::sendMessage(Auth::id(), $this->selectedUser->id, $this->newMessage);
            $this->newMessage = '';
            $this->loadMessages();
        }
    }

    public function render(): View
    {
        return view('livewire.chat');
    }
}

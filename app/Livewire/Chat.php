<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\User;
use App\Models\Message;
use Illuminate\Support\Facades\Auth;

class Chat extends Component
{
    public $users;
    public $messages;
    public $newMessage;
    public $selectedUser = null;
    public $pollingInterval;

    public function mount()
    {
        $this->pollingInterval = 1;
        $this->users = User::where('id', '!=', Auth::id())->orderBy('username')->get();
        $this->messages = collect();
    }

    public function selectUser($userId)
    {
        $this->selectedUser = User::find($userId);
        $this->loadMessages();
    }

    public function loadMessages()
    {
        if ($this->selectedUser) {
            $this->messages = Message::where(function ($query) {
                $query->where('user_id', Auth::id())
                      ->where('author_id', $this->selectedUser->id);
            })->orWhere(function ($query) {
                $query->where('user_id', $this->selectedUser->id)
                      ->where('author_id', Auth::id());
            })->orderBy('time', 'asc')->get();
        }
    }

    public function sendMessage()
    {
        if ($this->selectedUser && $this->newMessage) {
            Message::create([
                'user_id' => $this->selectedUser->id,
                'author_id' => Auth::id(),
                'text' => $this->newMessage,
                'read' => false,
                'time' => now(),
            ]);

            $this->newMessage = '';
            $this->loadMessages();
        }
    }

    public function render()
    {
        return view('livewire.chat');
    }
}
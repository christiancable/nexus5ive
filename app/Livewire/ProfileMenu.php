<?php

namespace App\Livewire;

use Illuminate\Http\Request;
use Livewire\Component;

class ProfileMenu extends Component
{
    public $user;

    public $notificationCount = 0;

    public $commentsCount = 0;

    public $messagesCount = 0;

    public $sectionsCount = 0;

    public $pollingInterval = 5;

    public function mount(Request $request)
    {
        $this->pollingInterval = config('nexus.notification_check_interval');
        $this->user = $request->user();
        $this->fetchNotifications();
    }

    public function fetchNotifications()
    {
        $this->sectionsCount = count($this->user->sections);
        $this->commentsCount = $this->user->newCommentCount();
        $this->messagesCount = $this->user->newMessageCount();
        $this->notificationCount = $this->commentsCount + $this->messagesCount;
    }

    public function render()
    {
        return view('livewire.profile-menu');
    }
}

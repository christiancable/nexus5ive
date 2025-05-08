<?php

namespace App\Livewire;

use App\Models\Report;
use Illuminate\Http\Request;
use Livewire\Component;

class ProfileMenu extends Component
{
    public $user;

    public $notificationCount = 0;

    public $commentsCount = 0;

    public $messagesCount = 0;

    public $sectionsCount = 0;

    public $unreadChats;

    public $pollingInterval = 5;

    public $reportsCount = 0;

    public function mount(Request $request)
    {
        $this->pollingInterval = config('nexus.notification_check_interval');
        $this->user = $request->user();
        $this->unreadChats = collect();
        $this->fetchNotifications();
    }

    public function fetchNotifications()
    {
        $this->sectionsCount = count($this->user->sections);
        $this->commentsCount = $this->user->newCommentCount();
        $this->messagesCount = $this->user->unreadChatCount();
        $this->notificationCount = $this->commentsCount + $this->messagesCount;
        $this->unreadChats = $this->user->unreadChats;

        // administrator see a count of open moderation reports with their notifications
        if ($this->user->isAdmin()) {
            $this->reportsCount = Report::open()->count();
            $this->notificationCount += $this->reportsCount;
        }
    }

    public function render()
    {
        return view('livewire.profile-menu');
    }
}

<?php

namespace App\Livewire;

use Illuminate\Http\Request;
use Livewire\Component;

class Mentions extends Component
{
    public $mentionsCount;

    public $mentions = [];

    public $user;

    public $pollingInterval = 5;

    public function mount(Request $request)
    {
        $this->pollingInterval = config('nexus.notification_check_interval');
        $this->user = $request->user();
        $this->fetchMentions();
    }

    public function fetchMentions()
    {
        $this->mentions = $this->user->mentions;
        $this->mentionsCount = count($this->mentions);
    }

    public function clearMentions()
    {
        $this->user->clearMentions();
        $this->fetchMentions();
    }

    public function render()
    {
        return view('livewire.mentions');
    }
}

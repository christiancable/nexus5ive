<?php

namespace App\Livewire;

use App\Models\User;
use Livewire\Attributes\Computed;
use Livewire\Component;

class Userlist extends Component
{
    public $search = '';

    #[Computed]
    public function users()
    {
        if (strlen($this->search) > 2)
            {
                return User::select('username', 'name', 'popname', 'latestLogin', 'totalPosts', 'totalVisits')
                    ->verified()
                    ->where('username', 'like', '%'.$this->search.'%')
                    ->orWhere('name', 'like', '%'.$this->search.'%')
                    ->orderBy('username', 'asc')
                    ->get();
            } else {
                return User::select('username', 'name', 'popname', 'latestLogin', 'totalPosts', 'totalVisits')
                    ->verified()
                    ->orderBy('username', 'asc')
                    ->get();
            }
    }

    public function render()
    {
        return view('livewire.userlist');
    }
}

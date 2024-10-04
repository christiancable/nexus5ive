<?php

namespace App\Livewire;

use App\Models\User;
use Livewire\Attributes\Computed;
// use Illuminate\Support\Facades\Log;
use Livewire\Component;

class Userlist extends Component
{
    public $search = '';

    #[Computed]
    public function users()
    {
        return User::select('username', 'name', 'popname', 'latestLogin', 'totalPosts', 'totalVisits')
            ->verified()
            ->where('username', 'like', '%'.$this->search.'%')
            ->orWhere('name', 'like', '%'.$this->search.'%')
            ->orderBy('username', 'asc')
            ->get();
    }

    public function render()
    {
        return view('livewire.userlist');
    }
}

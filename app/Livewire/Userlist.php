<?php

namespace App\Livewire;

use App\Models\User;
use Livewire\Component;

class Userlist extends Component
{
    public $users = null;
    public $search = '';

    public function mount()
    {
        if (!$this->users) {
            $users = $this->fetchUsers();
        }
        
    }

    public function fetchUsers()
    {
        return User::select('username', 'name', 'popname', 'latestLogin', 'totalPosts', 'totalVisits')->verified()->orderBy('username', 'asc')->get();
    }

    public function render()
    {
        return view('livewire.userlist', [
            'users' => $this->users->filter(function ($user) {
                $search = $this->search;
                return 
                // where(['username' => $this->search])->orWhere(['name' => $this->search])->get()
            }


            $filtered = $collection->filter(function (int $value, int $key) {
                return $value > 2;
            });
        ]);
    }
}

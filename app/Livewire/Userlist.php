<?php

namespace App\Livewire;

use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Livewire\Attributes\Computed;
use Livewire\Component;

class Userlist extends Component
{
    public $search = '';

    public Collection $allUsers;

    public function fetchUsers()
    {
        $seconds = 50 * 60;

        return Cache::remember('userslist', $seconds, function () {
            return User::select('username', 'name', 'popname', 'latestLogin', 'totalPosts', 'totalVisits')
                ->verified()
                ->orderBy('username', 'asc')
                ->get();
        });
    }

    #[Computed]
    public function users()
    {
        $allUsers = $this->fetchUsers();

        $results = collect();

        if (strlen($this->search) > 2) {
            $results = $allUsers->filter(function ($item, $key) {
                return Str::contains($item->username.' '.$item->name, $this->search, ignoreCase: true);
            });
        } else {
            $results = $allUsers;
        }

        return $results;
    }

    public function render()
    {
        return view('livewire.userlist');
    }
}

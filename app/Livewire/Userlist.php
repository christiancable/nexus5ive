<?php

namespace App\Livewire;

use App\Models\User;
use Illuminate\Support\Collection;
use Livewire\Attributes\Computed;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Livewire\Component;

class Userlist extends Component
{
    public string $search = '';

    public Collection $allUsers;

    public function mount()
    {
        $this->fetchUsers();
    }

    public function fetchUsers()
    {
        $seconds = 5 * 60;

        $this->allUsers = Cache::remember('userslist', $seconds, function () {
            return User::select('username', 'name', 'popname', 'latestLogin', 'totalPosts', 'totalVisits')
                ->verified()
                ->orderBy('username', 'asc')
                ->get();
        });
    }

    #[Computed]
    public function users()
    {
        $results = collect();

        if (strlen($this->search) > 2) {
            $results = $this->allUsers->filter(function ($key, $item) {
                return true;
                // return Str::contains($item['username'] . ' ' . $item['name'], $this->search, ignoreCase: true);
            });
        } else {
            $results = $this->allUsers;
        }

        logger('Search term: ' . $this->search . ' Results count: ' . $results->count());
        return $results;
    }

    public function render()
    {
        return view('livewire.userlist');
    }
}

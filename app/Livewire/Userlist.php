<?php

namespace App\Livewire;

use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Livewire\Attributes\Computed;
use Livewire\Component;

class Userlist extends Component
{
    public $search = '';

    public Collection $allUsers;

    /**
     * @return \Illuminate\Database\Eloquent\Collection<int, User>
     */
    public function fetchUsers(): \Illuminate\Database\Eloquent\Collection
    {
        $seconds = 5 * 60;

        return Cache::remember('userslist', $seconds, function () {
            return User::select('username', 'name', 'popname', 'latestLogin', 'totalPosts', 'totalVisits')
                ->verified()
                ->orderBy('username', 'asc')
                ->get();
        });
    }

    #[Computed]
    public function users(): Collection
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

    public function render(): View
    {
        return view('livewire.userlist');
    }
}

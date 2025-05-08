<?php

namespace App\View\Components;

use App\Models\User;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class ProfileLink extends Component
{
    /**
     * Create a new component instance.
     */
    public function __construct(
        public ?User $user = null,
        public ?string $url = null,
        public ?string $username = null
    ) {
        if ($user) {
            $this->url = action('App\Http\Controllers\Nexus\UserController@show', ['user' => $user->username]);
            $this->username = $user->username;
        }
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.profile-link');
    }
}

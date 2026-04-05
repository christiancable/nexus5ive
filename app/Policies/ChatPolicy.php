<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ChatPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return ! $user->is_guest && ! config('nexus.archive_mode');
    }

    public function create(User $user): bool
    {
        return ! $user->is_guest;
    }
}

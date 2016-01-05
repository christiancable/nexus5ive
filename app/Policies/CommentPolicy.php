<?php

namespace Nexus\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Nexus\Comment;
use Nexus\User;

class CommentPolicy
{
    use HandlesAuthorization;

    /**
     * Create a new policy instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Determine if the given user can delete the given comment.
     *
     * @param  User  $user
     * @param  Task  $task
     * @return bool
     */
    public function destroy(User $user, Comment $task)
    {
        return $user->id === $comment->user_id;
    }
}

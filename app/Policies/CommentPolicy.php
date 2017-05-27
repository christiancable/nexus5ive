<?php

namespace App\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use App\Comment;
use App\User;

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
     * @param  User     $user
     * @param  Comment  $comment
     * @return bool
     */
    public function destroy(User $user, Comment $comment)
    {
        return $user->id === $comment->user_id;
    }
}

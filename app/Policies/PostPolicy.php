<?php

namespace App\Policies;

use App\User;
use App\Post;
use App\Topic;
use Illuminate\Auth\Access\HandlesAuthorization;

class PostPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view the post.
     *
     * @param  \App\User  $user
     * @param  \App\Post  $post
     * @return mixed
     */
    public function view(User $user, Post $post)
    {
        //
    }

    /**
     * Determine whether the user can create posts.
     *
     * @param  App\User  $user
     * @param  App\Topic  $topic
     * @return bool
     */
    public function create(User $user, Topic $topic)
    {
        // admins can always create new posts
        if ($user->administrator) {
            return true;
        }
    
        // moderators can always create new posts in topics they moderate
        if ($user->id === $topic->section->moderator->id) {
            return true;
        }
    
        // other users can only create posts of the topic is not ready only
        if (!$topic->readonly) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can update the post.
     *
     * @param  \App\User  $user
     * @param  \App\Post  $post
     * @return mixed
     */
    public function update(User $user, Post $post)
    {
        //
    }

    /**
     * Determine whether the user can delete the post.
     *
     * @param  \App\User  $user
     * @param  \App\Post  $post
     * @return mixed
     */
    public function delete(User $user, Post $post)
    {
        // admins can always delete posts
        if ($user->administrator) {
            return true;
        }

        // moderators can always delete posts in topics they moderate
        if ($user->id === $post->topic->section->moderator->id) {
            return true;
        }

        return false;
    }
}

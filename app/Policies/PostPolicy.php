<?php

namespace App\Policies;

use App\Post;
use App\Topic;
use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class PostPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view the post.
     *
     * @return mixed
     */
    public function view(User $user, Post $post)
    {
        //
    }

    /**
     * Determine whether the user can create posts.
     *
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
        if (! $topic->readonly) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can update the post.
     *
     * a post can be edited at any time by
     * - topic moderator
     * - bbs administrator
     * - the creator of the post within X seconds if that post is the most recent in the topic
     *
     * @return bool
     */
    public function update(User $user, Post $post)
    {
        // admins can always edit posts
        if ($user->administrator) {
            return true;
        }

        // moderators can always delete posts in topics they moderate
        if ($user->id === $post->topic->section->moderator->id) {
            return true;
        }

        // is this the most recent post in this topic, is it by the logged in user and is it recent
        $latestPost = $post->topic->posts->last();

        if (
            ($post['id'] === $latestPost['id']) &&
            ($post->author->id == $user->id) &&
            ($post->time->diffInSeconds() <= config('nexus.recent_edit'))
        ) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can delete the post.
     *
     * @return bool
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

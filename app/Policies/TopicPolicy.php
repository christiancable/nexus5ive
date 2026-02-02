<?php

namespace App\Policies;

use App\Models\Section;
use App\Models\Topic;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class TopicPolicy
{
    use HandlesAuthorization;

    public function before(User $user, $ability)
    {
        // Administrators can perform any action on a topic
        if ($user->administrator) {
            return true;
        }
    }

    /**
     * Determine whether the user can view the topic.
     *
     * @return mixed
     */
    public function view(User $user, Topic $topic)
    {
        return true;
    }

    /**
     * a user can see post details if the topic is not anonymous
     * or if the user is privilaged
     */
    public function viewDetails(User $user, Topic $topic): bool
    {
        // all users can see details if the topic is not secret
        if (! $topic->secret) {
            return true;
        }

        // otherwise they only see them if they moderate the topic
        return $topic->section->moderator->id === $user->id;
    }

    /**
     * Determine whether the user can create topics.
     * Topics can be created by:
     * - the moderator of the current section
     * - bbs administrators (handled by before())
     * - any authenticated user if the section's allow_user_topics flag is true
     *
     * @return mixed
     */
    public function create(User $user, Section $section)
    {
        // Moderator can always create topics
        if ($user->id === $section->moderator->id) {
            return true;
        }

        // Any authenticated user can create if section allows it
        return $section->allow_user_topics;
    }

    /**
     * Determine whether the user can update the topic.
     *
     * User can update the topic if they are
     * - the moderator of the section
     * - a bbs administrator
     *
     * @return mixed
     */
    public function update(User $user, Topic $topic)
    {
        return $user->id === $topic->section->moderator->id;
    }

    /**
     * Determine whether the user can move the topic.
     * User can update the topic if they are
     * - the moderator of the topic's section
     * - & the moderator of the destination section
     * - or a bbs administrator
     *
     * - a section cannot be moved to be within itself
     *
     * @return bool
     */
    public function move(User $user, Topic $topic, Section $destinationSection)
    {
        if ($topic->id === $destinationSection->id) {
            return false;
        }

        return $user->id === $topic->section->moderator->id && $user->id === $destinationSection->moderator->id;
    }

    /**
     * Determine whether the user can delete the topic.
     *
     * User can delete the topic if
     * - they are the topic moderator
     * - OR they are an administrator
     *
     * @return mixed
     */
    public function delete(User $user, Topic $topic)
    {
        return $user->id === $topic->section->moderator->id;
    }

    /**
     * Determine whether the user can restore the section
     *
     * a topic can only be restored by
     * - the moderator of the topic's parent section
     * - the moderator of the destination section
     *
     * @return bool
     */
    public function restore(User $user, Topic $trashedTopic, Section $destinationSection)
    {
        return $user->id === $trashedTopic->section->moderator->id && $user->id === $destinationSection->moderator->id;
    }

    /**
     * Determine whether the user can reply or post to a topic.
     */
    public function reply(User $user, Topic $topic)
    {
        if ($topic->section->moderator->id === $user->id) {
            return true;
        }

        return ! $topic->readonly;
    }
}

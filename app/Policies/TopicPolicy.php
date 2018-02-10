<?php

namespace App\Policies;

use App\User;
use App\Topic;
use App\Section;
use Illuminate\Auth\Access\HandlesAuthorization;

class TopicPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view the topic.
     *
     * @param  \App\User  $user
     * @param  \App\Topic  $topic
     * @return mixed
     */
    public function view(User $user, Topic $topic)
    {
        //
    }

    /**
     * Determine whether the user can create topics.
     * Topics can be created by the moderator of the current section or bbs
     * administrators
     *
     * @param   \App\User  $user
     * @param   \App\Section $section
     * @return mixed
     */
    public function create(User $user, Section $section)
    {
        if ($user->adminstrator) {
            return true;
        }

        if ($user->id === $section->moderator->id) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can update the topic.
     *
     * @param  \App\User  $user
     * @param  \App\Topic  $topic
     * @return mixed
     */
    public function update(User $user, Topic $topic)
    {
        //
    }

    /**
     * Determine whether the user can delete the topic.
     *
     * @param  \App\User  $user
     * @param  \App\Topic  $topic
     * @return mixed
     */
    public function delete(User $user, Topic $topic)
    {
        //
    }

    /**
     * Determine whether the user can restore the section
     *
     * a topic can only be restored by
     * - the moderator of the topic's parent section
     * - the moderator of the destination section
     *
     * @param  \App\User  $user
     * @param  \App\Topic  $trashedTopic
     * @param  integer\App\Section  $destinationSection
     * @return mixed
     */
    public function restore(User $user, Topic $trashedTopic, Section $destinationSection)
    {
        return $user->id === $trashedTopic->section->moderator->id && $user->id === $destinationSection->moderator->id;
    }
}

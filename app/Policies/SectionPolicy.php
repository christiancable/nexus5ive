<?php

namespace App\Policies;

use App\Models\Section;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class SectionPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view the section.
     *
     * @return mixed
     */
    public function view(User $user, Section $section)
    {
        //
    }

    /**
     * a user can create a section if they moderate the current section
     *
     * @return bool
     */
    public function create(User $user, Section $currentSection)
    {
        return $user->id === $currentSection->moderator->id;
    }

    /**
     * a section can only be updated by the moderator or the moderator of the parent section
     *
     * @return bool
     */
    public function update(User $user, Section $section)
    {
        if ($user->id === $section->moderator->id) {
            return true;
        }

        // note the section might not have a parent
        if ($section->parent) {
            if ($section->parent->moderator->id === $user->id) {
                return true;
            }
        }

        return false;
    }

    /**
     * can a user move the section into the destinationSection
     *   - only if the user moderates the sections parent and the destination section
     *
     * @return bool
     */
    public function move(User $user, Section $section, Section $destinationSection)
    {
        return $user->id === $section->parent->moderator->id && $user->id === $destinationSection->moderator->id;
    }

    /**
     * Determine whether the user can delete the section.
     * - a user can delete a section if they are the moderator of the parent section
     *
     * @return bool
     */
    public function delete(User $user, Section $section)
    {
        return $user->id === $section->parent->moderator->id;
    }

    /**
     * Determine whether the user can restore the section
     *
     * a section can only be restored by user who
     * - moderates the trashed section
     * - moderates the destination section
     *
     * @return bool
     */
    public function restore(User $user, Section $trashedSection, Section $destinationSection)
    {
        return $user->id === $trashedSection->moderator->id && $user->id === $destinationSection->moderator->id;
    }
}

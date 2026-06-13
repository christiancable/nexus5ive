<?php

namespace Tests\Feature\Policies;

use App\Models\Section;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class SectionPolicyTest extends TestCase
{
    use RefreshDatabase;

    private User $sysop;

    private User $moderator;

    private User $unrelatedUser;

    private Section $home;

    private Section $section;

    private Section $childSection;

    protected function setUp(): void
    {
        parent::setUp();

        $this->sysop = User::factory()->forTheme()->create();
        $this->moderator = User::factory()->forTheme()->create();
        $this->unrelatedUser = User::factory()->forTheme()->create();

        $this->home = Section::factory()->for($this->sysop, 'moderator')->create(['parent_id' => null]);
        $this->section = Section::factory()->for($this->moderator, 'moderator')->for($this->home, 'parent')->create();
        $this->childSection = Section::factory()->for($this->unrelatedUser, 'moderator')->for($this->section, 'parent')->create();
    }

    // create

    #[Test]
    public function moderator_can_create_subsection_in_their_section(): void
    {
        $this->assertTrue($this->moderator->can('create', $this->section));
    }

    #[Test]
    public function non_moderator_cannot_create_subsection(): void
    {
        $this->assertFalse($this->unrelatedUser->can('create', $this->section));
    }

    // update

    #[Test]
    public function moderator_can_update_their_own_section(): void
    {
        $this->assertTrue($this->moderator->can('update', $this->section));
    }

    #[Test]
    public function parent_moderator_can_update_child_section(): void
    {
        $this->assertTrue($this->moderator->can('update', $this->childSection));
    }

    #[Test]
    public function unrelated_user_cannot_update_section(): void
    {
        $this->assertFalse($this->unrelatedUser->can('update', $this->section));
    }

    // move

    #[Test]
    public function parent_moderator_who_also_moderates_destination_can_move_section(): void
    {
        // sysop moderates home (the parent of $section) and also moderates a destination
        $destination = Section::factory()->for($this->sysop, 'moderator')->for($this->home, 'parent')->create();

        $this->assertTrue($this->sysop->can('move', [$this->section, $destination]));
    }

    #[Test]
    public function user_without_destination_moderation_cannot_move_section(): void
    {
        $destination = Section::factory()->for($this->sysop, 'moderator')->for($this->home, 'parent')->create();

        // moderator moderates $section's parent (home)... wait, no — sysop moderates home.
        // unrelatedUser moderates childSection but not $section's parent, so cannot move $section
        $this->assertFalse($this->unrelatedUser->can('move', [$this->section, $destination]));
    }

    // delete

    #[Test]
    public function parent_moderator_can_delete_child_section(): void
    {
        // sysop moderates home, which is the parent of $section
        $this->assertTrue($this->sysop->can('delete', $this->section));
    }

    #[Test]
    public function non_parent_moderator_cannot_delete_section(): void
    {
        $this->assertFalse($this->unrelatedUser->can('delete', $this->section));
    }

    // restore

    #[Test]
    public function user_moderating_both_trashed_section_and_destination_can_restore(): void
    {
        $destination = Section::factory()->for($this->moderator, 'moderator')->for($this->home, 'parent')->create();
        $this->section->delete();
        $trashed = Section::onlyTrashed()->find($this->section->id);

        $this->assertTrue($this->moderator->can('restore', [$trashed, $destination]));
    }

    #[Test]
    public function user_moderating_only_destination_cannot_restore(): void
    {
        $destination = Section::factory()->for($this->sysop, 'moderator')->for($this->home, 'parent')->create();
        $this->section->delete();
        $trashed = Section::onlyTrashed()->find($this->section->id);

        $this->assertFalse($this->sysop->can('restore', [$trashed, $destination]));
    }

    #[Test]
    public function user_moderating_only_trashed_section_cannot_restore(): void
    {
        $destination = Section::factory()->for($this->sysop, 'moderator')->for($this->home, 'parent')->create();
        $this->section->delete();
        $trashed = Section::onlyTrashed()->find($this->section->id);

        $this->assertFalse($this->moderator->can('restore', [$trashed, $destination]));
    }
}

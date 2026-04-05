<?php

namespace Tests\Browser;

use App\Models\Post;
use App\Models\Section;
use App\Models\Topic;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use PHPUnit\Framework\Attributes\Test;
use Tests\DuskTestCase;

class GuestAccessTest extends DuskTestCase
{
    use DatabaseMigrations;

    protected User $guest;

    protected User $normalUser;

    protected Section $home;

    protected Section $section;

    protected Topic $topic;

    protected function setUp(): void
    {
        parent::setUp();

        $this->guest = User::factory()->create(['is_guest' => true]);
        $this->normalUser = User::factory()->create();

        $this->home = Section::factory()
            ->for($this->normalUser, 'moderator')
            ->create(['parent_id' => null]);

        $this->section = Section::factory()
            ->for($this->normalUser, 'moderator')
            ->for($this->home, 'parent')
            ->create();

        $this->topic = Topic::factory()
            ->for($this->section)
            ->create(['readonly' => false]);

        Post::factory()
            ->for($this->topic)
            ->for($this->normalUser, 'author')
            ->create();
    }

    // ── Subscribe button ─────────────────────────────────────────────────────

    #[Test]
    public function guest_cannot_see_subscribe_button_on_topic(): void
    {
        $this->browse(function ($browser) {
            $browser->loginAs($this->guest)
                ->visit('/topic/'.$this->topic->id)
                ->assertDontSee('Unsubscribe from this topic')
                ->assertDontSee('Subscribe to this topic');
        });
    }

    // ── Profile page ─────────────────────────────────────────────────────────

    #[Test]
    public function guest_sees_read_only_profile_not_edit_form(): void
    {
        $this->browse(function ($browser) {
            $browser->loginAs($this->guest)
                ->visit('/user/'.$this->guest->username)
                ->assertDontSee('Save Changes');
        });
    }

    // ── Comment form ─────────────────────────────────────────────────────────

    #[Test]
    public function guest_cannot_see_comment_form_on_profile(): void
    {
        $this->browse(function ($browser) {
            $browser->loginAs($this->guest)
                ->visit('/user/'.$this->normalUser->username)
                ->assertDontSee('Add Comment');
        });
    }
}

<?php

namespace Tests\Browser;

use App\Models\Post;
use App\Models\Section;
use App\Models\Topic;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use Tests\DuskTestCase;

class MentionsTest extends DuskTestCase
{
    use DatabaseMigrations;

    protected $user;

    protected $home;

    protected $topic;

    protected $anotherUser;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        $this->anotherUser = User::factory()->create();
        $this->home = Section::factory()->create([
            'parent_id' => null,
            'user_id' => $this->user->id,
        ]);
        $this->topic = Topic::factory()->create([
            'section_id' => $this->home->id,
        ]);
    }

    /**
     * SLOW (~6s): assertMissing waits for implicit timeout before asserting element is absent.
     */
    #[Test]
    #[Group('slow')]
    public function userWithNoMentionsDoesNotSeeOptionToClearMentions(): void
    {
        $user = $this->user;

        $this->browse(function ($browser) use ($user) {
            $browser->loginAs($user)
                ->visit('/')
                ->assertMissing('@mentions-count');
        });
    }

    #[Test]
    public function userWithMentionsCanSeeTheyHaveMentions(): void
    {
        // GIVEN we have a user with no mentions
        $user = $this->user;

        // WHEN that user is mentioned in a topic
        $post = Post::factory()->create([
            'topic_id' => $this->topic->id,
            'user_id' => $this->anotherUser->id,
        ]);
        $this->user->addMention($post);

        $this->browse(function ($browser) use ($user) {
            // THEN the user can see they have mentions
            $browser->loginAs($user)
                ->visit('/')
                ->assertPresent('@mentions-count');
        });
    }

    /**
     * SLOW (~11s): waitUntilMissing has implicit wait timeout for element to disappear.
     */
    #[Test]
    #[Group('slow')]
    public function userWithMentionsCanClearMentions(): void
    {
        // GIVEN we have a user with no mentions
        $user = $this->user;

        // WHEN that user is mentioned in a topic
        $post = Post::factory()->create([
            'topic_id' => $this->topic->id,
            'user_id' => $this->anotherUser->id,
        ]);
        $this->user->addMention($post);

        $this->browse(function ($browser) use ($user) {
            // WHEN the user clears all mentions
            $browser->loginAs($user);

            $browser
                ->visit('/')
                ->click('@mentions-menu-toggle')
                ->press('@mentions-clear')
                ->waitUntilMissing('@mentions-count');

            // THEN the user no-longer sees they have mentions
            $browser->assertMissing('@mentions-count');
        });
    }
}

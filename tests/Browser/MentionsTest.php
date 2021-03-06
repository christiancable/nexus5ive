<?php

namespace Tests\Browser;

use App\User;
use App\Post;
use App\Topic;
use App\Section;
use Tests\DuskTestCase;
use Laravel\Dusk\Browser;
use Illuminate\Foundation\Testing\DatabaseMigrations;

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
            'section_id' => $this->home->id
        ]);
    }

    /**
     * @test
     */
    public function userWithNoMentionsDoesNotSeeOptionToClearMentions()
    {
        $user = $this->user;

        $this->browse(function ($browser) use ($user) {
            $browser->loginAs($user)
                    ->visit('/')
                    ->assertMissing('@mentions-count');
        });
    }

    /**
     * @test
     */
    public function userWithMentionsCanSeeTheyHaveMentions()
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
     * @test
     */
    public function userWithMentionsCanClearMentions()
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
            $browser->loginAs($user)
                    ->visit('/')
                    ->click('@mentions-menu-toggle')
                    ->press('@mentions-clear');

            // THEN the user no-longer sees they have mentions
            $browser->loginAs($user)
                    ->visit('/')
                    ->assertMissing('@mentions-count');
        });
    }
}

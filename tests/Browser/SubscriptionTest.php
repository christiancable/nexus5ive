<?php

namespace Tests\Browser;

use App\Section;
use App\Topic;
use App\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\DuskTestCase;

class SubscriptionTest extends DuskTestCase
{
    use DatabaseMigrations;

    protected $user;

    protected $home;

    protected $topic;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        $this->home = Section::factory()->create([
            'parent_id' => null,
            'user_id' => $this->user->id,
        ]);
        $this->topic = Topic::factory()->create([
            'section_id' => $this->home->id,
        ]);
    }

    /**
     * @test
     */
    public function userCanUnsubscribeFromTopic()
    {
        /*
        GIVEN a user, a section, a topic
        WHEN the user clicks 'Unsubscribe from this topic'
        THEN the user does not see 'Unsubscribe from this topic'
        AND the user sees 'Subscribe to this topic'
        */

        $user = $this->user;
        $topic = $this->topic;

        $this->browse(function ($browser) use ($user, $topic) {
            $browser->loginAs($user)
                ->visit('/topic/'.$topic->id)
                ->press('Unsubscribe from this topic')
                ->visit('/topic/'.$topic->id)
                ->assertSee('Subscribe to this topic');
        });
    }

    /**
     * @test
     */
    public function userCanResubscribeToTopic()
    {
        /*
        GIVEN a user, a section, a topic which the user is unsubscribed from
        WHEN the user visit the topic
        AND clicks 'Subscribe to this topic'
        THEN the user sees 'Unsubscribe from this topic' instead of
            'Subscribe to this topic'
        */

        $user = $this->user;
        $topic = $this->topic;
        \App\Helpers\ViewHelper::unsubscribeFromTopic($user, $topic);
        $this->browse(function ($browser) use ($user, $topic) {
            $browser->loginAs($user)
                ->visit('/topic/'.$topic->id)
                ->press('Subscribe to this topic')
                ->visit('/topic/'.$topic->id)
                ->assertSee('Unsubscribe from this topic');
        });
    }
}

<?php

namespace Tests\Browser;

use App\Post;
use App\Section;
use App\Topic;
use App\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use PHPUnit\Framework\Attributes\Test;
use Tests\DuskTestCase;

class NextTest extends DuskTestCase
{
    use DatabaseMigrations;
    use DatabaseMigrations;

    protected $user;

    protected $home;

    protected $topic;

    protected $post;

    protected $section1;

    protected $section2;

    protected $topic1;

    protected $topic2;

    protected $postPreview;

    protected $noTopicsMsg = 'No updated topics found. Why not start a new conversation or read more sections?';

    protected $newTopicsMsg = 'People have been talking! New posts found in ';

    protected function setUp(): void
    {
        parent::setUp();

        /*
        sets up bbs structure with two sub sections each with a topic

        home
            - section1
                - topic1
            - section2
                - topic2
        */

        $this->user = User::factory()->create();
        $this->home = Section::factory()->create([
            'parent_id' => null,
            'user_id' => $this->user->id,
        ]);

        $this->section1 = Section::factory()->create([
            'parent_id' => $this->home->id,
            'user_id' => $this->user->id,
        ]);
        $this->section2 = Section::factory()->create([
            'parent_id' => $this->home->id,
            'user_id' => $this->user->id,
        ]);

        $this->topic1 = Topic::factory()->create([
            'section_id' => $this->home->id,
        ]);
        $this->topic2 = Topic::factory()->create([
            'section_id' => $this->home->id,
        ]);
    }

    #[Test]
    public function userCanJumpToNextUpdatedTopic(): void
    {
        /*
        GIVEN we have a bbs with sections and topic which the user is subscribed to
        WHEN a post is added to that topic
        AND user clicks 'Next'
        THEN user goes to the section containing topic
        AND use sees the updated topics found message
        */
        $user = $this->user;
        $newTopicsMsg = $this->newTopicsMsg;
        $topic1 = $this->topic1;

        \App\Helpers\ViewHelper::subscribeToTopic($user, $this->topic1);

        Post::factory()->create([
            'topic_id' => $this->topic1->id,
            'user_id' => $this->user->id,
        ]);

        $this->browse(function ($browser) use ($user, $newTopicsMsg, $topic1) {
            $browser->loginAs($user)
                ->visit('/')
                ->press('@toolbar-next')
                ->assertPathIs('/section/'.$topic1->section->id)
                ->assertSee($newTopicsMsg.$topic1->title);
        });
    }

    #[Test]
    public function userDoesNotJumpToTopicWhenNoTopicHasBeenUpdated(): void
    {
        /*
        GIVEN we have a bbs with sections and no unread topics
        WHEN user clicks 'Next'
        THEN the user jumps to the home section
        AND use sees the no updated topics found message
        */
        $user = $this->user;
        $noTopicsMsg = $this->noTopicsMsg;

        $this->browse(function ($browser) use ($user, $noTopicsMsg) {
            $browser->loginAs($user)
                ->visit('/')
                ->press('@toolbar-next')
                ->assertPathIs('/section/'.$this->home->id)
                ->assertSee($noTopicsMsg);
        });
    }

    #[Test]
    public function userDoesNotJumpToNextUnsubscribedTopic(): void
    {
        /*
        GIVEN we have a bbs with sections and a topic which is unsubscribed from
        WHEN a post is added to the unsubscribed topic
        AND user clicks 'Next'
        THEN user stays on the current section
        AND use sees the no updated topics found message
        */
        $user = $this->user;
        $noTopicsMsg = $this->noTopicsMsg;

        \App\Helpers\ViewHelper::unsubscribeFromTopic($user, $this->topic1);

        Post::factory()->create([
            'topic_id' => $this->topic1->id,
            'user_id' => $this->user->id,
        ]);

        $this->browse(function ($browser) use ($user, $noTopicsMsg) {
            $browser->loginAs($user)
                ->visit('/')
                ->press('@toolbar-next')
                ->assertPathIs('/section/'.$this->home->id)
                ->assertSee($noTopicsMsg);
        });
    }

    #[Test]
    public function userCanMarkAllSubscribedTopicsAsRead(): void
    {
        /*
        GIVEN there is an updated topic
        WHEN the user clicks the Next button
        AND the user clicks the 'mark all subscribed topics as read' link
        THEN the user see the 'Success! all subscribed topics are now marked as read' message
        AND WHEN the user clicks the Next button
        THEN the user sees the no updated topics found message
        */

        $user = $this->user;
        $noTopicsMsg = $this->noTopicsMsg;

        \App\Helpers\ViewHelper::subscribeToTopic($user, $this->topic1);

        Post::factory()->create([
            'topic_id' => $this->topic1->id,
            'user_id' => $this->user->id,
        ]);

        $this->browse(function ($browser) use ($user, $noTopicsMsg) {
            $browser->loginAs($user)
                ->visit('/')
                ->press('@toolbar-next')
                ->clickLink('mark all subscribed topics as read')
                ->press('@toolbar-next')
                ->assertSee($noTopicsMsg);
        });
    }
}

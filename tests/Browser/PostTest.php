<?php

namespace Tests\Browser;

use App\Http\Controllers\Nexus\TopicController;
use App\Models\Section;
use App\Models\Topic;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use Tests\DuskTestCase;

class PostTest extends DuskTestCase
{
    use DatabaseMigrations;

    protected User $sysop;

    protected User $moderator;

    protected User $normalUser;

    protected Section $home;

    protected Section $subSection;

    protected Topic $topic;

    protected Topic $closedTopic;

    protected function setUp(): void
    {
        parent::setup();

        $this->sysop = User::factory()->create(['administrator' => true]);
        $this->moderator = User::factory()->create();
        $this->normalUser = User::factory()->create();

        $this->home = Section::factory()->for($this->sysop, 'moderator')->create([
            'parent_id' => null,
        ]);
        $this->subSection = Section::factory()
            ->for($this->moderator, 'moderator')
            ->for($this->home, 'parent')
            ->create();

        $this->topic = Topic::factory()
            ->for($this->subSection, 'section')
            ->create();

        $this->closedTopic = Topic::factory()
            ->for($this->subSection, 'section')
            ->create([
                'readonly' => true,
            ]);
    }

    /**
     * Optimized: replaced waitForReload with waitForText for posted content.
     */
    #[Test]
    public function userCanPostInTopic(): void
    {
        $this->browse(function ($browser) {
            $title = 'Hello Everyone!';
            $text = 'this is a test go back to sleep';

            $browser->loginAs($this->normalUser)
                // when a user visits a topic and leaves a post
                ->visit(action([TopicController::class, 'show'], ['topic' => $this->topic]))
                ->type('title', $title)
                ->type('text', $text)
                ->press('Add Comment')
                // wait for the post to appear on the page
                ->waitForText($title)
                // and the post is seen within the topic
                ->assertSeeIn('.card-title', $title)
                ->assertSee($text);
        });
    }

    #[Test]
    public function userCannotPostInReadOnlyTopic(): void
    {
        $this->browse(function ($browser) {
            $title = 'Hello Everyone!';
            $text = 'this is a test go back to sleep';

            $browser->loginAs($this->normalUser)
                // when a user visits a topic and leaves a post
                ->visit(action([TopicController::class, 'show'], ['topic' => $this->closedTopic]))
                ->assertDontSee('Add Comment')
                ->assertSee(strip_tags(__('nexus.topic.closed.normal')));
        });
    }

    /**
     * Optimized: replaced waitForReload with waitForText for posted content.
     */
    #[Test]
    public function ownerCanPostInReadOnlyTopicWithWarning(): void
    {
        $this->browse(function ($browser) {
            $title = 'Hello Everyone!';
            $text = 'this is a test go back to sleep';

            $browser->loginAs($this->moderator)
                // when a moderator visits a read only topic and leaves a post
                ->visit(action([TopicController::class, 'show'], ['topic' => $this->closedTopic]))
                // they see a notice that the topic is closed but can post
                ->assertSee(strip_tags(__('nexus.topic.closed.moderator')))
                ->type('title', $title)
                ->type('text', $text)
                ->press('Add Comment')
                // wait for the post to appear on the page
                ->waitForText($title)
                // and the post is seen within the topic
                ->assertSeeIn('.card-title', $title)
                ->assertSee($text);
        });
    }

    /**
     * Optimized: replaced waitForReload with waitForText for posted content.
     */
    #[Test]
    public function adminCanPostInReadOnlyTopicWithWarning(): void
    {
        $this->browse(function ($browser) {
            $title = 'Hello Everyone!';
            $text = 'this is a test go back to sleep';

            $browser->loginAs($this->sysop)
                // when a sysop visits a read only topic and leaves a post
                ->visit(action([TopicController::class, 'show'], ['topic' => $this->closedTopic]))
                // they see a notice that the topic is closed but can post
                ->assertSee(strip_tags(__('nexus.topic.closed.moderator')))
                ->type('title', $title)
                ->type('text', $text)
                ->press('Add Comment')
                // wait for the post to appear on the page
                ->waitForText($title)
                // and the post is seen within the topic
                ->assertSeeIn('.card-title', $title)
                ->assertSee($text);
        });
    }

    /**
     * Optimized: waitFor with short timeout since validation is immediate.
     */
    #[Test]
    public function userCannotPostEmptyPostInTopic(): void
    {
        $this->browse(function ($browser) {
            $title = 'Hello Everyone!';

            $browser->loginAs($this->normalUser)
                // when a user visits a topic and leaves a post
                ->visit(action([TopicController::class, 'show'], ['topic' => $this->topic]))
                ->type('title', $title)
                ->press('Add Comment')
                ->waitFor('.alert-danger', 5)
                ->assertSeeIn('.alert-danger', strip_tags(__('nexus.validation.post.empty')));
        });
    }
}

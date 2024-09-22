<?php

namespace Tests\Browser;

use App\Http\Controllers\Nexus\TopicController;
use App\Models\Post;
use App\Models\Section;
use App\Models\Topic;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
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
    }

    #[Test]
    public function userCanPostInTopic(): void
    {
        /*
        user can compose the post
        press the button
        see the post in the redirected page
        */
        $this->browse(function ($browser) {
            $browser->loginAs($this->normalUser)
                ->visit(action([TopicController::class, 'show'], ['topic' => $this->topic]))
                ->type('title', 'Hello Everyone!')
                ->type('text', 'this is a text post to say hello')
                ->waitForLiveWire(function ($browser) {
                    $browser->press('Add Comment');
                })
                ->screenshot('testing');

            // see title in .card-title and text in .card-text
        });
    }

    public function userCannotPostInReadOnlyTopic(): void {}

    public function ownerCanPostInReadOnlyTopicWithWarning(): void {}

    public function adminCanPostInReadOnlyTopicWithWarning(): void {}

    public function userCannotPostEmptyPostInTopic(): void {}
}

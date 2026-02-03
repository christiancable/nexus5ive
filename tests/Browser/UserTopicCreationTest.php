<?php

namespace Tests\Browser;

use App\Http\Controllers\Nexus\SectionController;
use App\Models\Post;
use App\Models\Section;
use App\Models\Topic;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use Tests\DuskTestCase;

class UserTopicCreationTest extends DuskTestCase
{
    use DatabaseMigrations;

    protected User $moderator;

    protected User $normalUser;

    protected Section $home;

    protected Section $section;

    protected function setUp(): void
    {
        parent::setUp();

        $this->moderator = User::factory()->create();
        $this->normalUser = User::factory()->create();

        $this->home = Section::factory()
            ->for($this->moderator, 'moderator')
            ->create(['parent_id' => null]);

        $this->section = Section::factory()
            ->for($this->moderator, 'moderator')
            ->for($this->home, 'parent')
            ->create(['allow_user_topics' => false]);
    }

    #[Test]
    public function section_edit_form_has_allow_user_topics_checkbox(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->moderator)
                ->visit(action([SectionController::class, 'show'], ['section' => $this->section]));

            // Use JavaScript to show the edit form directly
            $browser->script("document.querySelector('#section-edit{$this->section->id}').classList.add('show', 'active');");

            $browser->pause(300)
                ->assertPresent('#allow_user_topics_'.$this->section->id)
                ->assertSee('Allow all users to create topics');
        });
    }

    #[Test]
    public function moderator_sees_all_topic_options(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->moderator)
                ->visit(action([SectionController::class, 'show'], ['section' => $this->section]))
                ->click('[data-bs-target="#addTopicForm"]')
                ->waitFor('#addTopicForm.show')
                ->assertVisible('input#secret[type="checkbox"]')
                ->assertVisible('input#readonly[type="checkbox"]')
                ->assertVisible('select[name="weight"]');
        });
    }

    #[Test]
    public function normal_user_does_not_see_add_topic_when_disabled(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->normalUser)
                ->visit(action([SectionController::class, 'show'], ['section' => $this->section]))
                ->assertDontSee('Add New Topic');
        });
    }

    #[Test]
    public function normal_user_sees_add_topic_when_enabled(): void
    {
        $this->section->update(['allow_user_topics' => true]);

        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->normalUser)
                ->visit(action([SectionController::class, 'show'], ['section' => $this->section]))
                ->assertSee('Add New Topic');
        });
    }

    /**
     * SLOW (~16s): Multiple assertMissing calls wait for implicit timeout before asserting.
     */
    #[Test]
    #[Group('slow')]
    public function normal_user_does_not_see_moderator_only_options(): void
    {
        $this->section->update(['allow_user_topics' => true]);

        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->normalUser)
                ->visit(action([SectionController::class, 'show'], ['section' => $this->section]))
                ->click('[data-bs-target="#addTopicForm"]')
                ->waitFor('#addTopicForm.show')
                ->assertMissing('input[name="secret"][type="checkbox"]')
                ->assertMissing('input[name="readonly"][type="checkbox"]')
                ->assertMissing('select[name="weight"]');
        });
    }

    /**
     * SLOW (~31s): waitForText after form submission waits for page content to appear.
     */
    #[Test]
    #[Group('slow')]
    public function normal_user_can_create_topic(): void
    {
        $this->section->update(['allow_user_topics' => true]);

        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->normalUser)
                ->visit(action([SectionController::class, 'show'], ['section' => $this->section]))
                ->click('[data-bs-target="#addTopicForm"]')
                ->waitFor('#addTopicForm.show')
                ->type('title', 'My New Topic')
                ->type('intro', 'This is my topic introduction')
                ->press('Add Topic')
                ->waitForText('My New Topic');

            // Verify topic was created with default values
            $topic = Topic::where('title', 'My New Topic')->first();
            $this->assertNotNull($topic);
            $this->assertEquals(0, $topic->secret);
            $this->assertEquals(0, $topic->readonly);
            $this->assertEquals(0, $topic->weight);

            // Verify an initial post was created
            $post = Post::where('topic_id', $topic->id)->first();
            $this->assertNotNull($post);
            $this->assertEquals('This is my topic introduction', $post->text);
            $this->assertEquals($this->normalUser->id, $post->user_id);
        });
    }
}

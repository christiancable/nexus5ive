<?php

namespace Tests\Browser;

use App\Models\Post;
use App\Models\Section;
use App\Models\Topic;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Facades\Artisan;
use Laravel\Dusk\Browser;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use Tests\DuskTestCase;

class SectionInfoTest extends DuskTestCase
{
    use DatabaseMigrations;

    protected $user;

    protected $home;

    protected $subSection;

    protected $topicInSubSection;

    protected $anotherTopicInSubSection;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->forTheme()->create();
        $this->home = Section::factory()->create([
            'parent_id' => null,
            'user_id' => $this->user->id,
        ]);
        $this->subSection = Section::factory()->create([
            'parent_id' => $this->home,
            'user_id' => $this->user->id,
        ]);
        $this->topicInSubSection = Topic::factory()->create([
            'section_id' => $this->subSection->id,
        ]);
        $this->anotherTopicInSubSection = Topic::factory()->create([
            'section_id' => $this->subSection->id,
        ]);

        // we cache the latest post info so clear the cache between tests
        // Artisan::call('cache:clear'); // Removed
    }

    /**
     * SLOW/FLAKY (~3-30s): waitForText with cache invalidation; intermittent Chrome timeouts.
     */
    #[Test]
    #[Group('slow')]
    public function section_info_shows_which_topic_has_the_most_recent_post(): void
    {
        /*
        GIVEN we have a section with a sub-section
        WHEN a post is added one a topic within the sub-section
        THEN the user can see which topic contains the latest post
        */

        $user = $this->user;
        $home = $this->home;

        $newPost = Post::factory()->create([
            'topic_id' => $this->topicInSubSection->id,
            'user_id' => $this->user->id,
        ]);

        Section::forgetMostRecentPostAttribute($home->id);
        Section::forgetMostRecentPostAttribute($this->subSection->id);

        $topicInSubSection = $this->topicInSubSection;

        $this->browse(function (Browser $browser) use ($user, $home, $topicInSubSection) {
            $browser->loginAs($user)
                ->visit('/section/'.$home->id)
                ->waitForText('Latest Post in '.$topicInSubSection->title, 5);
        });
    }

    /**
     * SLOW/FLAKY (~30s): assertDontSee waits for implicit timeout; Chrome renderer timeouts.
     */
    #[Test]
    #[Group('slow')]
    public function section_with_no_topics_shows_no_topic_as_having_the_most_recent_post(): void
    {
        /*
        GIVEN we have a section with a sub-section
        AND there is a topic within the sub-section with no posts
        THEN the user can see there are not latest posts for that sub-section
        */

        $user = $this->user;
        $home = $this->home;

        $this->browse(function (Browser $browser) use ($user, $home) {
            $browser->loginAs($user)
                ->visit('/section/'.$home->id)
                ->assertDontSee('Latest Post in ');
        });
    }

    /**
     * SLOW/FLAKY (~1-3s): Multiple waitForText calls with cache invalidation between visits.
     */
    #[Test]
    #[Group('slow')]
    public function section_info_updates_latest_post_found_in_when_new_posts_are_added(): void
    {
        /*
        GIVEN we have a section with a sub-section
        WHEN a post is added one a topic within the sub-section
        THEN the user can see which topic contains the latest post
        WHEN a post is added to a different topic
        THEN the user can see that different topic contains the latest post
        */

        $user = $this->user;
        $home = $this->home;

        $newPost = Post::factory()->create([
            'topic_id' => $this->topicInSubSection->id,
            'user_id' => $this->user->id,
        ]);

        Section::forgetMostRecentPostAttribute($home->id);
        Section::forgetMostRecentPostAttribute($this->subSection->id);

        $topicInSubSection = $this->topicInSubSection;
        $anotherTopicInSubSection = $this->anotherTopicInSubSection;

        $this->browse(function (Browser $browser) use ($user, $home, $topicInSubSection, $anotherTopicInSubSection) {
            $browser->loginAs($user)
                ->visit('/section/'.$home->id)
                ->waitForText('Latest Post in '.$topicInSubSection->title, 5);

            $anotherNewPost = Post::factory()->create([
                'topic_id' => $anotherTopicInSubSection->id,
                'user_id' => $user->id,
            ]);

            Section::forgetMostRecentPostAttribute($home->id);
            Section::forgetMostRecentPostAttribute($this->subSection->id);

            $browser->loginAs($user)
                ->visit('/section/'.$home->id)
                ->waitForText('Latest Post in '.$anotherTopicInSubSection->title, 5);
        });
    }
}

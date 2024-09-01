<?php

namespace Tests\Browser;

use App\Post;
use App\Section;
use App\Topic;
use App\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Facades\Artisan;
use Laravel\Dusk\Browser;
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

        $this->user = User::factory()->create();
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
        Artisan::call('cache:clear');
    }

    #[Test]
    public function sectionInfoShowsWhichTopicHasTheMostRecentPost()
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

        $topicInSubSection = $this->topicInSubSection;

        $this->browse(function (Browser $browser) use ($user, $home, $topicInSubSection) {
            $browser->loginAs($user)
                ->visit('/section/'.$home->id)
                ->assertSee('Latest Post in '.$topicInSubSection->title);
        });
    }

    #[Test]
    public function sectionWithNoTopicsShowsNoTopicAsHavingTheMostRecentPost()
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

    #[Test]
    public function sectionInfoUpdatesLatestPostFoundInWhenNewPostsAreAdded()
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

        $topicInSubSection = $this->topicInSubSection;
        $anotherTopicInSubSection = $this->anotherTopicInSubSection;

        $this->browse(function (Browser $browser) use ($user, $home, $topicInSubSection, $anotherTopicInSubSection) {
            $browser->loginAs($user)
                ->visit('/section/'.$home->id)
                ->assertSee('Latest Post in '.$topicInSubSection->title);

            $anotherNewPost = Post::factory()->create([
                'topic_id' => $anotherTopicInSubSection->id,
                'user_id' => $user->id,
            ]);

            $browser->loginAs($user)
                ->visit('/section/'.$home->id)
                ->assertSee('Latest Post in '.$anotherTopicInSubSection->title);
        });
    }
}

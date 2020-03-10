<?php

namespace Tests\Browser;

use App\User;
use App\Post;
use App\Topic;
use App\Section;
use Tests\DuskTestCase;
use Laravel\Dusk\Browser;
use Illuminate\Foundation\Testing\DatabaseMigrations;

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

        $this->user = factory(User::class)->create();
        $this->home = factory(Section::class)->create([
           'parent_id' => null,
           'user_id' => $this->user->id,
        ]);
        $this->subSection = factory(Section::class)->create([
           'parent_id' => $this->home,
           'user_id' => $this->user->id,
        ]);
        $this->topicInSubSection = factory(Topic::class)->create([
           'section_id' => $this->subSection->id
        ]);
        $this->anotherTopicInSubSection = factory(Topic::class)->create([
           'section_id' => $this->subSection->id
        ]);
    }

    /**
     * @test
     */
    public function sectionInfoShowsWhichTopicHasTheMostRecentPost()
    {
        /*
        GIVEN we have a section with a sub-section
        WHEN a post is added one a topic within the sub-section
        THEN the user can see which topic contains the latest post
        */

        $user = $this->user;
        $home = $this->home;
        
        $newPost = factory(Post::class)->create([
            'topic_id' => $this->topicInSubSection->id,
            'user_id' => $this->user->id,
        ]);

        $topicInSubSection = $this->topicInSubSection;

        $this->browse(function (Browser $browser) use ($user, $home, $topicInSubSection) {
            $browser->loginAs($user)
                    ->visit('/section/' . $home->id)
                    ->assertSee('Latest Post in ' . $topicInSubSection->title);
        });
    }


    /**
     * @test
     */
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
                    ->visit('/section/' . $home->id)
                    ->assertDontSee('Latest Post in ');
        });
    }


    /**
     * @test
     */
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
        
        $newPost = factory(Post::class)->create([
           'topic_id' => $this->topicInSubSection->id,
           'user_id' => $this->user->id,
        ]);

        $topicInSubSection = $this->topicInSubSection;
        $anotherTopicInSubSection = $this->anotherTopicInSubSection;

        $this->browse(function (Browser $browser) use ($user, $home, $topicInSubSection, $anotherTopicInSubSection) {
            $browser->loginAs($user)
                    ->visit('/section/' . $home->id)
                    ->assertSee('Latest Post in ' . $topicInSubSection->title);

            $anotherNewPost = factory(Post::class)->create([
                'topic_id' => $anotherTopicInSubSection->id,
                'user_id' => $user->id,
            ]);

            $browser->loginAs($user)
                    ->visit('/section/' . $home->id)
                    ->assertSee('Latest Post in ' . $anotherTopicInSubSection->title);
        });
    }
}

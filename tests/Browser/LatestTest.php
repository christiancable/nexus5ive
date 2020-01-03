<?php

namespace Tests\Browser;

use App\User;
use App\Post;
use App\Topic;
use App\Section;
use Tests\DuskTestCase;
use Laravel\Dusk\Browser;
use App\Helpers\NxCodeHelper;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class LatestTest extends DuskTestCase
{
    use DatabaseMigrations;

    protected $user;
    protected $home;
    protected $topic;
    protected $post;
    protected $postPreview;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = factory(User::class)->create();
        $this->home = factory(Section::class)->create([
            'parent_id' => null,
            'user_id' => $this->user->id,
        ]);
        $this->topic = factory(Topic::class)->create([
            'section_id' => $this->home->id
        ]);
        $this->post = factory(Post::class)->create([
            'topic_id' => $this->topic->id,
            'user_id' => $this->user->id,
        ]);

        $this->postPreview = substr(strip_tags(NxCodeHelper::nxDecode($this->post->text)), 0, 140);
    }

    /**
     * @test
     */
    public function userSeesPostPreviewForTopicWithPosts()
    {
        /*
        GIVEN we have a topic with posts
        WHEN we visit the latest posts page
        THEN should see the text of the latest post
        */

        $user = $this->user;
        $postPreview = $this->postPreview;

        $this->browse(function ($browser) use ($user, $postPreview) {
            $browser->loginAs($user)
                    ->visit('/section/latest')
                    ->assertSee($postPreview);
        });
    }

    /**
     * @test
     */
    public function userDoesNotSeePostPreviewForUnsubscribedTopicWithPosts()
    {
        /*
        GIVEN we have a topic with posts
        WHEN the user unsubscribes from the topic
        AND we visit the latest posts page
        THEN we should not see the text of the latest post
        */

        $user = $this->user;
        $topic = $this->topic;
        $postPreview = $this->postPreview;

        $this->browse(function ($browser) use ($user, $topic, $postPreview) {
            $browser->loginAs($user)
                    ->visit('/topic/' . $topic->id)
                    ->press('Unsubscribe from this topic');
            
            $browser->loginAs($user)
                    ->visit('/section/latest/')
                    ->assertDontSee($postPreview);
        });
    }
}

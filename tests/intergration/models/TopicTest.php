<?php

namespace Tests\Intergration\Models;

use App\Post;
use App\User;
use App\Topic;
use App\Section;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class TopicTest extends TestCase
{
    use RefreshDatabase;


    protected $user;
    protected $home;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        $this->home = Section::factory()->for($this->user, 'moderator')->create([
            'parent_id' => null,
        ]);
    }


    /**
     * @test
     */
    public function deletingTopicSoftDeletesItsPosts()
    {
        // GIVEN we have a topic with posts
        $topic = Topic::factory()->for($this->home)->create();
        Post::factory()->count(20)->for(User::factory(), 'author')->create(['topic_id' => $topic->id]);

        // we have 1 topic with 20 posts
        $this->assertEquals(Topic::all()->count(), 1);
        $this->assertEquals(Post::where('topic_id', $topic->id)->count(), 20);

        // WHEN we archive the topic
        $topic->delete();

        // THEN we have no topics and no posts
        $this->assertEquals(Topic::all()->count(), 0);
        $this->assertEquals(Post::all()->count(), 0);

        // BUT we have 1 trashed topic and 20 trashed posts
        $this->assertEquals(Topic::withTrashed()->count(), 1);
        $this->assertEquals(Post::withTrashed()->where('topic_id', $topic->id)->count(), 20);
    }

    public function mostRecentPostTimeReturnsTimeOfLatestPost()
    {
        $faker = \Faker\Factory::create();

        // GIVEN we have a topic with posts
        $topic = Topic::factory()->for($this->home)->create();

        // posts from the last month but not today
        Post::factory()
            ->for($topic)
            ->count(20)
            ->create(
                [
                    'time' => $faker->dateTimeThisMonth('-1 days')
                ]
            );

        // the most recent post being from today
        $newPost = Post::factory()
            ->for($topic)
            ->create(
                [
                    'time' => new \DateTime('now')
                ]
            );

        // WHEN we look at look at the MostRecentPostTime
        // THEN we have the date of the most recent post
        $this->assertEquals($topic->most_recent_post_time, $newPost->time);
    }
}

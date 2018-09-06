<?php

namespace Tests\Intergration\Models;

use App\Post;
use App\User;
use App\Topic;
use Tests\BrowserKitTestCase;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class TopicTest extends BrowserKitTestCase
{
    use DatabaseTransactions;
    
    /**
     * @test
     */
    public function deletingTopicSoftDeletesItsPosts()
    {
        // GIVEN we have a topic with post
        $topic = factory(Topic::class)->create();
        factory(Post::class, 20)->create(['topic_id' => $topic->id]);
    
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
        $topic = factory(Topic::class)->create();

        // posts from the last month but not today
        factory(Post::class, 20)
            ->create(
                ['topic_id' => $topic->id,
                'time' => $faker->dateTimeThisMonth('-1 days')]
            );

        // the most recent post being from today
        $newPost = factory(Post::class)
            ->create(
                ['topic_id' => $topic->id,
                'time' => new \DateTime('now')]
            );

        // WHEN we look at look at the MostRecentPostTime
        // THEN we have the date of the most recent post
        $this->assertEquals($topic->most_recent_post_time, $newPost->time);
    }
}

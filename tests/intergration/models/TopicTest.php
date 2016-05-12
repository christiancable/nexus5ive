<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Nexus\Topic;
use Nexus\Post;
use Nexus\User;

class TopicTest extends TestCase
{
    use DatabaseTransactions;
    
    public function test_deleting_topic_soft_deletes_its_posts()
    {
        // GIVEN we have a topic with post
        $topic = factory(Topic::class, 1)->create();
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

    public function test_MostRecentPostTime_returns_time_of_latest_post()
    {
        $faker = \Faker\Factory::create();

        // GIVEN we have a topic with posts
        $topic = factory(Topic::class, 1)->create();

        // posts from the last month but not today
        factory(Post::class, 20)
            ->create(
                ['topic_id' => $topic->id,
                'time' => $faker->dateTimeThisMonth('-1 days')]
            );

        // the most recent post being from today
        $newPost = factory(Post::class, 1)
            ->create(
                ['topic_id' => $topic->id,
                'time' => new \DateTime('now')]
            );

        // WHEN we look at look at the MostRecentPostTime
        // THEN we have the date of the most recent post
        $this->assertEquals($topic->most_recent_post_time, $newPost->time);
    }

    public function test_mostRecentlyReadPostDate_returns_time_of_most_recently_read_post_time()
    {
        $faker = \Faker\Factory::create();

        // GIVEN  we have a user
        $user = factory(User::class, 1)->create();

        // AND we have a topic with posts
        $topic = factory(Topic::class, 1)->create();
        factory(Post::class, 20)
            ->create(
                ['topic_id' => $topic->id,
                'time' => $faker->dateTimeThisMonth('-2 days')]
            );
            
        // AND the most recent post being from yesterday
        $newPost = factory(Post::class, 1)
            ->create(
                ['topic_id' => $topic->id,
                'time' => $faker->dateTimeThisMonth('-1 days')]
            );
          
        // WHEN the user reads the topic
        \Nexus\Helpers\ViewHelper::updateReadProgress($user, $topic);

        // THEN the date of the most recent post in the topic matches
        // the one most recently read by the user
        $this->assertEquals(
            $topic->most_recent_post_time,
            $topic->mostRecentlyReadPostDate($user->id)
        );

        // WHEN a new post is added now
        $anotherPost = factory(Post::class, 1)
            ->create(
                ['topic_id' => $topic->id,
                'time' => new \DateTime('now')]
            );

        // THEN the date of the most recent post in the topic does not matches
        // the one most recently read by the user
        $this->assertNotEquals(
            $topic->most_recent_post_time,
            $topic->mostRecentlyReadPostDate($user->id)
        );

    }
}
 /*
    @todo

    test that unreadPosts returns the posts which are unread by that user
 */

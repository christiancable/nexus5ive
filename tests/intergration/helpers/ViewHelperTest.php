<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use App\User;
use App\Topic;
use App\Post;

/*
 @todo: unsubscribe status - once we have an unsubscribe method

*/
class ViewHelperTest extends BrowserKitTestCase
{
    use DatabaseTransactions;
    
    public function test_getReadProgress_returns_time_of_most_recently_read_post_time()
    {
        $faker = \Faker\Factory::create();

        // GIVEN  we have a user
        $user = factory(App\User::class)->create();

        // AND we have a topic with posts
        $topic = factory(Topic::class)->create();
        factory(App\Post::class, 20)
            ->create(
                ['topic_id' => $topic->id,
                'time' => $faker->dateTimeThisMonth('-2 days')]
            );
            
        // AND the most recent post being from yesterday
        $newPost = factory(App\Post::class)
            ->create(
                ['topic_id' => $topic->id,
                'time' => $faker->dateTimeThisMonth('-1 days')]
            );
          
        // WHEN the user reads the topic
        \App\Helpers\ViewHelper::updateReadProgress($user, $topic);

        // THEN the date of the most recent post in the topic matches
        // the one most recently read by the user
        $this->assertEquals(
            $topic->most_recent_post_time,
            \App\Helpers\ViewHelper::getReadProgress($user, $topic)
        );

        // WHEN a new post is added now
        $anotherPost = factory(App\Post::class)
            ->create(
                ['topic_id' => $topic->id,
                'time' => new \DateTime('now')]
            );

        // THEN the date of the most recent post in the topic does not matches
        // the one most recently read by the user
        $this->assertNotEquals(
            $topic->most_recent_post_time,
            \App\Helpers\ViewHelper::getReadProgress($user, $topic)
        );
    }

    public function test_getTopicStatus_indicates_new_posts_for_a_topic_with_new_posts()
    {
        $faker = \Faker\Factory::create();

        // GIVEN we have a user
         $user = factory(App\User::class)->create();
        // AND we have a topic
        // with posts
        // AND we have a topic with posts
        $topic = factory(Topic::class)->create();
        factory(App\Post::class, 20)
            ->create(
                ['topic_id' => $topic->id,
                'time' => $faker->dateTimeThisMonth('-2 days')]
            );
            
        // AND the user has read the topic
        \App\Helpers\ViewHelper::updateReadProgress($user, $topic);

        // WHEN a new post is added
        $anotherPost = factory(App\Post::class)
            ->create(
                ['topic_id' => $topic->id,
                'time' => new \DateTime('now')]
            );

        // THEN the topic appears to have new posts to the user
        $topicStatus = \App\Helpers\ViewHelper::getTopicStatus($user, $topic);

        $this->assertTrue($topicStatus['new_posts']);
    }

    public function test_getTopicStatus_indicates_no_new_posts_for_a_topic_with_no_new_posts()
    {
        $faker = \Faker\Factory::create();

        // GIVEN we have a user
         $user = factory(App\User::class)->create();
        // AND we have a topic
        // with posts
        // AND we have a topic with posts
        $topic = factory(Topic::class)->create();
        factory(App\Post::class, 20)
            ->create(
                ['topic_id' => $topic->id,
                'time' => $faker->dateTimeThisMonth('-2 days')]
            );
            
        // AND the user has read the topic
        \App\Helpers\ViewHelper::updateReadProgress($user, $topic);

        // THEN the topic appears to have no new posts to the user
        $topicStatus = \App\Helpers\ViewHelper::getTopicStatus($user, $topic);

        $this->assertFalse($topicStatus['new_posts']);
    }

    public function test_getTopicStatus_indicates_never_read_for_a_never_viewed_topic()
    {
        $faker = \Faker\Factory::create();

        // GIVEN we have a user
         $user = factory(App\User::class)->create();
        
        // WHEN we add a topic
        $topic = factory(Topic::class)->create();
        
        // THEN the topic appears to have new to the user
        $topicStatus = \App\Helpers\ViewHelper::getTopicStatus($user, $topic);

        $this->assertTrue($topicStatus['never_read']);
    }

    public function test_getTopicStatus_does_not_indicate_never_read_for_a_viewed_topic()
    {
        $faker = \Faker\Factory::create();

        // GIVEN we have a user
         $user = factory(App\User::class)->create();
        
        // AND we add a topic
        $topic = factory(Topic::class)->create();
        
        // WHEN the user has read the topic
        \App\Helpers\ViewHelper::updateReadProgress($user, $topic);

        // THEN the topic does not appear new to the user
        $topicStatus = \App\Helpers\ViewHelper::getTopicStatus($user, $topic);

        $this->assertFalse($topicStatus['never_read']);
    }

    public function test_getTopicStatus_returns_unsubscribed_when_a_user_unsubscribes()
    {
        $faker = \Faker\Factory::create();

        // GIVEN we have a user
         $user = factory(App\User::class)->create();
        
        // AND we add a topic
        $topic = factory(Topic::class)->create();

        // WHEN the user is unsubscribed from the topic
        \App\Helpers\ViewHelper::unsubscribeFromTopic($user, $topic);

        // THEN the topic does not appear new to the user
        $topicStatus = \App\Helpers\ViewHelper::getTopicStatus($user, $topic);

        $this->assertTrue($topicStatus['unsubscribed']);
    }

    public function test_getTopicStatus_returns_subscribed_when_a_user_resubscribes()
    {
        $faker = \Faker\Factory::create();

        // GIVEN we have a user
         $user = factory(App\User::class)->create();
        
        // AND we add a topic
        $topic = factory(Topic::class)->create();

        // WHEN the user is unsubscribed from the topic
        \App\Helpers\ViewHelper::unsubscribeFromTopic($user, $topic);

        // THEN the topic does not appear new to the user
        $topicStatus = \App\Helpers\ViewHelper::getTopicStatus($user, $topic);

        $this->assertTrue($topicStatus['unsubscribed']);

        // WHEN the user is unsubscribed from the topic
        \App\Helpers\ViewHelper::subscribeToTopic($user, $topic);

        // THEN the topic does not appear new to the user
        $topicStatus = \App\Helpers\ViewHelper::getTopicStatus($user, $topic);

        $this->assertFalse($topicStatus['unsubscribed']);
    }
}

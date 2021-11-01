<?php

namespace Tests\Intergration\Helpers;

use App\User;
use App\Post;
use App\Topic;
use Faker\Factory;
use Tests\TestCase;
use App\Helpers\ViewHelper;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ViewHelperTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @test
     */
    public function getReadProgressReturnsTimeOfMostRecentlyReadPost()
    {
        $faker = Factory::create();

        // GIVEN  we have a user
        $user = User::factory()->create();

        // AND we have a topic with posts
        $topic = Topic::factory()->create();
        Post::factory()->count(20)
            ->create(
                ['topic_id' => $topic->id,
                'time' => $faker->dateTimeThisMonth('-2 days')]
            );

        // AND the most recent post being from yesterday
        $newPost = Post::factory()
            ->create(
                ['topic_id' => $topic->id,
                'time' => $faker->dateTimeThisMonth('-1 days')]
            );

        // WHEN the user reads the topic
        ViewHelper::updateReadProgress($user, $topic);

        // THEN the date of the most recent post in the topic matches
        // the one most recently read by the user
        $this->assertEquals(
            $topic->most_recent_post_time,
            ViewHelper::getReadProgress($user, $topic)
        );

        // WHEN a new post is added now
        $anotherPost = Post::factory()
            ->create(
                ['topic_id' => $topic->id,
                'time' => new \DateTime('now')]
            );

        // THEN the date of the most recent post in the topic does not matches
        // the one most recently read by the user
        $this->assertNotEquals(
            $topic->most_recent_post_time,
            ViewHelper::getReadProgress($user, $topic)
        );
    }

    /**
     * @test
     */
    public function getTopicStatusIndicatesNewPostsForTopicWithNewPosts()
    {
        $faker = Factory::create();

        // GIVEN we have a user
         $user = User::factory()->create();
        // AND we have a topic
        // with posts
        // AND we have a topic with posts
        $topic = Topic::factory()->create();
        Post::factory()->count(20)
            ->create(
                ['topic_id' => $topic->id,
                'time' => $faker->dateTimeThisMonth('-2 days')]
            );

        // AND the user has read the topic
        ViewHelper::updateReadProgress($user, $topic);

        // WHEN a new post is added
        $anotherPost = Post::factory()
            ->create(
                ['topic_id' => $topic->id,
                'time' => new \DateTime('now')]
            );

        // THEN the topic appears to have new posts to the user
        $topicStatus = ViewHelper::getTopicStatus($user, $topic);

        $this->assertTrue($topicStatus['new_posts']);
    }

    /**
     * @test
     */
    public function getTopicStatusIndicatesNoNewPostsForTopicWithNoNewPosts()
    {
        $faker = Factory::create();

        // GIVEN we have a user
         $user = User::factory()->create();
        // AND we have a topic
        // with posts
        // AND we have a topic with posts
        $topic = Topic::factory()->create();
        Post::factory()
            ->count(20)
            ->create(
                ['topic_id' => $topic->id,
                'time' => $faker->dateTimeThisMonth('-2 days')]
            );

        // AND the user has read the topic
        ViewHelper::updateReadProgress($user, $topic);

        // THEN the topic appears to have no new posts to the user
        $topicStatus = ViewHelper::getTopicStatus($user, $topic);

        $this->assertFalse($topicStatus['new_posts']);
    }

    /**
     * @test
     */
    public function getTopicStatusIndicatesNeverReadForANeverViewedTopic()
    {
        $faker = Factory::create();

        // GIVEN we have a user
         $user = User::factory()->create();

        // WHEN we add a topic
        $topic = Topic::factory()->create();
        // THEN the topic appears to have new to the user
        $topicStatus = ViewHelper::getTopicStatus($user, $topic);

        $this->assertTrue($topicStatus['never_read']);
    }

    /**
     * @test
     */
    public function getTopicStatusDoesNotIndicateNeverReadForViewedTopic()
    {
        $faker = Factory::create();

        // GIVEN we have a user
         $user = User::factory()->create();

        // AND we add a topic
        $topic = Topic::factory()->create();

        // WHEN the user has read the topic
        ViewHelper::updateReadProgress($user, $topic);

        // THEN the topic does not appear new to the user
        $topicStatus = ViewHelper::getTopicStatus($user, $topic);

        $this->assertFalse($topicStatus['never_read']);
    }

    /**
     * @test
     */
    public function getTopicStatusReturnsUnsubscribedWhenUserUnsubscribes()
    {
        $faker = Factory::create();

        // GIVEN we have a user
         $user = User::factory()->create();

        // AND we add a topic
        $topic = Topic::factory()->create();

        // WHEN the user is unsubscribed from the topic
        ViewHelper::unsubscribeFromTopic($user, $topic);

        // THEN the topic does not appear new to the user
        $topicStatus = ViewHelper::getTopicStatus($user, $topic);

        $this->assertTrue($topicStatus['unsubscribed']);
    }

    /**
     * @test
     */
    public function getTopicStatusReturnsSubscribedWhenUserResubscribes()
    {
        $faker = Factory::create();

        // GIVEN we have a user
         $user = User::factory()->create();

        // AND we add a topic
        $topic = Topic::factory()->create();

        // WHEN the user is unsubscribed from the topic
        ViewHelper::unsubscribeFromTopic($user, $topic);

        // THEN the topic does not appear new to the user
        $topicStatus = ViewHelper::getTopicStatus($user, $topic);

        $this->assertTrue($topicStatus['unsubscribed']);

        // WHEN the user is unsubscribed from the topic
        ViewHelper::subscribeToTopic($user, $topic);

        // THEN the topic does not appear new to the user
        $topicStatus = ViewHelper::getTopicStatus($user, $topic);

        $this->assertFalse($topicStatus['unsubscribed']);
    }
}

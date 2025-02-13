<?php

namespace Tests\Intergration\Helpers;

use App\Helpers\ViewHelper;
use App\Models\Post;
use App\Models\Section;
use App\Models\Topic;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class ViewHelperTest extends TestCase
{
    use RefreshDatabase;

    public $faker;

    public $sysop;

    public $home;

    public function setUp(): void
    {
        parent::setUp();
        $this->faker = \Faker\Factory::create();
        $this->sysop = User::factory()->create();
        $this->home = Section::factory()->for($this->sysop, 'moderator')->create(['parent_id' => null]);
    }

    #[Test]
    public function getReadProgressReturnsTimeOfMostRecentlyReadPost(): void
    {
        // GIVEN we have a topic with posts
        $topic = Topic::factory()->for($this->home, 'section')->create();
        Post::factory()->count(20)
            ->for($this->sysop, 'author')
            ->for($topic, 'topic')
            ->create(
                ['time' => $this->faker->dateTimeThisMonth('-2 days')]
            );

        // AND the most recent post being from yesterday
        $newPost = Post::factory()
            ->for($topic, 'topic')
            ->for($this->sysop, 'author')
            ->create(
                ['time' => $this->faker->dateTimeThisMonth('-1 days')]
            );

        // WHEN the user reads the topic
        $user = User::factory()->create();
        ViewHelper::updateReadProgress($user, $topic);

        // THEN the date of the most recent post in the topic matches
        // the one most recently read by the user
        $this->assertEquals(
            $topic->most_recent_post_time,
            ViewHelper::getReadProgress($user, $topic)
        );

        // WHEN a new post is added now
        $anotherPost = Post::factory()
            ->for($topic, 'topic')
            ->for($this->sysop, 'author')
            ->create(
                ['time' => new \DateTime('now')]
            );

        // THEN the date of the most recent post in the topic does not matches
        // the one most recently read by the user
        $this->assertNotEquals(
            $topic->most_recent_post_time,
            ViewHelper::getReadProgress($user, $topic)
        );
    }

    #[Test]
    public function getTopicStatusIndicatesNewPostsForTopicWithNewPosts(): void
    {
        // GIVEN we have a topic with some posts
        $topic = Topic::factory()->for($this->home, 'section')->create();
        Post::factory()->count(20)
            ->for($this->sysop, 'author')
            ->for($topic, 'topic')
            ->create(
                ['time' => $this->faker->dateTimeThisMonth('-2 days')]
            );

        // AND the user has read the topic
        $user = User::factory()->create();
        ViewHelper::updateReadProgress($user, $topic);

        // WHEN a new post is added
        $anotherPost = Post::factory()
            ->for($topic, 'topic')
            ->for($this->sysop, 'author')
            ->create(
                ['time' => new \DateTime('now')]
            );

        // THEN the topic appears to have new posts to the user
        $topicStatus = ViewHelper::getTopicStatus($user, $topic);

        $this->assertTrue($topicStatus['new_posts']);
    }

    #[Test]
    public function getTopicStatusIndicatesNoNewPostsForTopicWithNoNewPosts(): void
    {

        // GIVEN a topic with some posts
        $topic = Topic::factory()->for($this->home, 'section')->create();
        Post::factory()
            ->count(20)
            ->for($topic, 'topic')
            ->for($this->sysop, 'author')
            ->create(
                ['time' => $this->faker->dateTimeThisMonth('-2 days')]
            );

        // AND the user has read the topic
        $user = User::factory()->create();
        ViewHelper::updateReadProgress($user, $topic);

        // THEN the topic appears to have no new posts to the user
        $topicStatus = ViewHelper::getTopicStatus($user, $topic);

        $this->assertFalse($topicStatus['new_posts']);
    }

    #[Test]
    public function getTopicStatusIndicatesNeverReadForANeverViewedTopic(): void
    {
        // GIVEN we have a user
        $user = User::factory()->create();

        // WHEN we add a topic
        $topic = Topic::factory()
            ->for($this->home, 'section')
            ->create();
        // THEN the topic appears to have new to the user
        $topicStatus = ViewHelper::getTopicStatus($user, $topic);

        $this->assertTrue($topicStatus['never_read']);
    }

    #[Test]
    public function getTopicStatusDoesNotIndicateNeverReadForViewedTopic(): void
    {
        // GIVEN we have a user
        $user = User::factory()->create();

        // AND we add a topic
        $topic = Topic::factory()->for($this->home, 'section')->create();

        // WHEN the user has read the topic
        ViewHelper::updateReadProgress($user, $topic);

        // THEN the topic does not appear new to the user
        $topicStatus = ViewHelper::getTopicStatus($user, $topic);

        $this->assertFalse($topicStatus['never_read']);
    }

    #[Test]
    public function getTopicStatusReturnsUnsubscribedWhenUserUnsubscribes(): void
    {
        // GIVEN we have a user
        $user = User::factory()->create();

        // AND we add a topic
        $topic = Topic::factory()->for($this->home, 'section')->create();

        // WHEN the user is unsubscribed from the topic
        ViewHelper::unsubscribeFromTopic($user, $topic);

        // THEN the topic does not appear new to the user
        $topicStatus = ViewHelper::getTopicStatus($user, $topic);

        $this->assertTrue($topicStatus['unsubscribed']);
    }

    #[Test]
    public function getTopicStatusReturnsSubscribedWhenUserResubscribes(): void
    {
        // GIVEN we have a user
        $user = User::factory()->create();

        // AND we add a topic
        $topic = Topic::factory()->for($this->home, 'section')->create();

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

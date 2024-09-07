<?php

namespace Tests\Intergration\Models;

use App\Post;
use App\Section;
use App\Topic;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class TopicTest extends TestCase
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
        $this->home = Section::factory()
            ->for($this->sysop, 'moderator')
            ->create(['parent_id' => null]);
    }

    #[Test]
    public function deletingTopicSoftDeletesItsPosts(): void
    {
        // GIVEN we have a topic with posts
        $topic = Topic::factory()
            ->for($this->home, 'section')
            ->create();

        Post::factory()
            ->for($topic, 'topic')
            ->for($this->sysop, 'author')
            ->count(20)->create();

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

    #[Test]
    public function mostRecentPostTimeReturnsTimeOfLatestPost(): void
    {
        // GIVEN we have a topic with posts
        $topic = Topic::factory()
            ->for($this->home, 'section')
            ->create();

        // posts from the last month but not today
        Post::factory()
            ->for($topic, 'topic')
            ->for($this->sysop, 'author')
            ->count(20)
            ->create(['time' => $this->faker->dateTimeThisMonth('-1 days')]);

        // the most recent post being from today
        $newPost = Post::factory()
            ->for($topic, 'topic')
            ->for($this->sysop, 'author')
            ->create(['time' => new \DateTime('now')]);

        // WHEN we look at look at the MostRecentPostTime
        // THEN we have the date of the most recent post
        $this->assertEquals($topic->most_recent_post_time, $newPost->time);
    }
}

<?php

namespace Tests\Feature;

use App\Models\Post;
use App\Models\Section;
use App\Models\Topic;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class TopicOrderingTest extends TestCase
{
    use RefreshDatabase;

    protected User $moderator;

    protected Section $home;

    protected function setUp(): void
    {
        parent::setUp();

        $this->moderator = User::factory()->create();

        $this->home = Section::factory()
            ->for($this->moderator, 'moderator')
            ->create(['parent_id' => null]);
    }

    #[Test]
    public function topics_ordered_by_weight_when_section_does_not_allow_user_topics(): void
    {
        $section = Section::factory()
            ->for($this->moderator, 'moderator')
            ->for($this->home, 'parent')
            ->create(['allow_user_topics' => false]);

        Topic::factory()->for($section)->create(['weight' => 3, 'title' => 'Topic Weight 3']);
        Topic::factory()->for($section)->create(['weight' => 1, 'title' => 'Topic Weight 1']);
        Topic::factory()->for($section)->create(['weight' => 2, 'title' => 'Topic Weight 2']);

        $topics = $section->topics()->get();

        $this->assertEquals('Topic Weight 1', $topics[0]->title);
        $this->assertEquals('Topic Weight 2', $topics[1]->title);
        $this->assertEquals('Topic Weight 3', $topics[2]->title);
    }

    #[Test]
    public function sticky_has_no_effect_on_ordering_when_section_does_not_allow_user_topics(): void
    {
        $section = Section::factory()
            ->for($this->moderator, 'moderator')
            ->for($this->home, 'parent')
            ->create(['allow_user_topics' => false]);

        Topic::factory()->for($section)->create(['weight' => 1, 'sticky' => false, 'title' => 'Topic Weight 1']);
        Topic::factory()->for($section)->create(['weight' => 2, 'sticky' => true, 'title' => 'Topic Weight 2 Sticky']);
        Topic::factory()->for($section)->create(['weight' => 3, 'sticky' => false, 'title' => 'Topic Weight 3']);

        $topics = $section->topics()->get();

        // Sticky topic should NOT be first - weight ordering takes precedence
        $this->assertEquals('Topic Weight 1', $topics[0]->title);
        $this->assertEquals('Topic Weight 2 Sticky', $topics[1]->title);
        $this->assertEquals('Topic Weight 3', $topics[2]->title);
    }

    #[Test]
    public function topics_ordered_by_most_recent_post_when_section_allows_user_topics(): void
    {
        $section = Section::factory()
            ->for($this->moderator, 'moderator')
            ->for($this->home, 'parent')
            ->create(['allow_user_topics' => true]);

        $topic1 = Topic::factory()->for($section)->create(['title' => 'Oldest Topic']);
        $topic2 = Topic::factory()->for($section)->create(['title' => 'Middle Topic']);
        $topic3 = Topic::factory()->for($section)->create(['title' => 'Newest Topic']);

        // Create posts with different times
        Post::factory()->for($topic1)->for($this->moderator, 'author')->create(['time' => now()->subDays(3)]);
        Post::factory()->for($topic2)->for($this->moderator, 'author')->create(['time' => now()->subDays(1)]);
        Post::factory()->for($topic3)->for($this->moderator, 'author')->create(['time' => now()]);

        $topics = $section->topics()->get();

        // Most recent activity first
        $this->assertEquals('Newest Topic', $topics[0]->title);
        $this->assertEquals('Middle Topic', $topics[1]->title);
        $this->assertEquals('Oldest Topic', $topics[2]->title);
    }

    #[Test]
    public function sticky_topic_appears_first_when_section_allows_user_topics(): void
    {
        $section = Section::factory()
            ->for($this->moderator, 'moderator')
            ->for($this->home, 'parent')
            ->create(['allow_user_topics' => true]);

        $topic1 = Topic::factory()->for($section)->create(['title' => 'Old Sticky Topic', 'sticky' => true]);
        $topic2 = Topic::factory()->for($section)->create(['title' => 'New Regular Topic', 'sticky' => false]);

        // The sticky topic has an older post
        Post::factory()->for($topic1)->for($this->moderator, 'author')->create(['time' => now()->subDays(7)]);
        // The regular topic has a newer post
        Post::factory()->for($topic2)->for($this->moderator, 'author')->create(['time' => now()]);

        $topics = $section->topics()->get();

        // Sticky topic should be first despite having older posts
        $this->assertEquals('Old Sticky Topic', $topics[0]->title);
        $this->assertEquals('New Regular Topic', $topics[1]->title);
    }

    #[Test]
    public function multiple_topics_ordered_correctly_with_one_sticky_in_user_topic_section(): void
    {
        $section = Section::factory()
            ->for($this->moderator, 'moderator')
            ->for($this->home, 'parent')
            ->create(['allow_user_topics' => true]);

        $topic1 = Topic::factory()->for($section)->create(['title' => 'Topic A', 'sticky' => false]);
        $topic2 = Topic::factory()->for($section)->create(['title' => 'Topic B Sticky', 'sticky' => true]);
        $topic3 = Topic::factory()->for($section)->create(['title' => 'Topic C', 'sticky' => false]);

        // Create posts - Topic A is newest, Topic B (sticky) is oldest, Topic C is middle
        Post::factory()->for($topic1)->for($this->moderator, 'author')->create(['time' => now()]);
        Post::factory()->for($topic2)->for($this->moderator, 'author')->create(['time' => now()->subDays(10)]);
        Post::factory()->for($topic3)->for($this->moderator, 'author')->create(['time' => now()->subDays(5)]);

        $topics = $section->topics()->get();

        // Sticky first, then by most recent post time
        $this->assertEquals('Topic B Sticky', $topics[0]->title);
        $this->assertEquals('Topic A', $topics[1]->title);
        $this->assertEquals('Topic C', $topics[2]->title);
    }

    #[Test]
    public function newly_created_topic_without_posts_appears_first_in_user_topic_section(): void
    {
        $section = Section::factory()
            ->for($this->moderator, 'moderator')
            ->for($this->home, 'parent')
            ->create(['allow_user_topics' => true]);

        // Create an older topic with an old post
        $oldTopic = Topic::factory()->for($section)->create([
            'title' => 'Old Topic',
            'created_at' => now()->subDays(10),
        ]);
        Post::factory()->for($oldTopic)->for($this->moderator, 'author')->create(['time' => now()->subDays(5)]);

        // Create a new topic without any posts (simulates moderator-created topic)
        Topic::factory()->for($section)->create([
            'title' => 'New Topic Without Posts',
            'created_at' => now(),
        ]);

        $topics = $section->topics()->get();

        // New topic should appear first based on created_at fallback
        $this->assertEquals('New Topic Without Posts', $topics[0]->title);
        $this->assertEquals('Old Topic', $topics[1]->title);
    }

    #[Test]
    public function topic_with_new_post_appears_before_topic_without_posts(): void
    {
        $section = Section::factory()
            ->for($this->moderator, 'moderator')
            ->for($this->home, 'parent')
            ->create(['allow_user_topics' => true]);

        // Create an older topic without posts
        Topic::factory()->for($section)->create([
            'title' => 'Topic Without Posts',
            'created_at' => now()->subDays(1),
        ]);

        // Create a topic with a very recent post
        $topicWithPost = Topic::factory()->for($section)->create([
            'title' => 'Topic With Recent Post',
            'created_at' => now()->subDays(5),
        ]);
        Post::factory()->for($topicWithPost)->for($this->moderator, 'author')->create(['time' => now()]);

        $topics = $section->topics()->get();

        // Topic with recent post should appear first
        $this->assertEquals('Topic With Recent Post', $topics[0]->title);
        $this->assertEquals('Topic Without Posts', $topics[1]->title);
    }
}

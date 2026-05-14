<?php

namespace Tests\Feature;

use App\Helpers\ViewHelper;
use App\Http\Controllers\Nexus\SectionController;
use App\Models\Post;
use App\Models\Section;
use App\Models\Topic;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class LeapTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    private Section $home;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        $this->home = Section::factory()
            ->for($this->user, 'moderator')
            ->create(['parent_id' => null]);
    }

    private function makeSection(): Section
    {
        return Section::factory()
            ->for($this->user, 'moderator')
            ->for($this->home, 'parent')
            ->create();
    }

    private function makeTopic(Section $section): Topic
    {
        return Topic::factory()->for($section, 'section')->create();
    }

    // Subscribe to a topic and immediately mark it as read so it starts clean.
    private function subscribeAndRead(Topic $topic): void
    {
        ViewHelper::subscribeToTopic($this->user, $topic);
        ViewHelper::updateReadProgress($this->user, $topic);
    }

    // Add a post at a time guaranteed to be after the topic's created_at,
    // so the View's latest_view_date (set at subscription time) will not match.
    private function addUnreadPost(Topic $topic): Post
    {
        return Post::factory()
            ->for($topic, 'topic')
            ->for($this->user, 'author')
            ->create(['time' => $topic->created_at->addMinute()]);
    }

    // -------------------------------------------------------------------------

    #[Test]
    public function redirects_to_home_with_warning_when_user_has_no_subscribed_topics(): void
    {
        $response = $this->actingAs($this->user)->get('/leap');

        $response->assertRedirect(
            action([SectionController::class, 'show'], ['section' => $this->home->id])
        );
        $response->assertSessionHas('headerAlert', function ($alert) {
            return $alert['level'] === 'warning';
        });
    }

    #[Test]
    public function redirects_to_home_with_warning_when_all_subscribed_topics_are_fully_read(): void
    {
        $section = $this->makeSection();
        $topic = $this->makeTopic($section);
        $this->addUnreadPost($topic);
        $this->subscribeAndRead($topic);

        $response = $this->actingAs($this->user)->get('/leap');

        $response->assertRedirect(
            action([SectionController::class, 'show'], ['section' => $this->home->id])
        );
        $response->assertSessionHas('headerAlert', function ($alert) {
            return $alert['level'] === 'warning';
        });
    }

    #[Test]
    public function redirects_to_the_section_containing_the_unread_topic(): void
    {
        $section = $this->makeSection();
        $topic = $this->makeTopic($section);
        ViewHelper::subscribeToTopic($this->user, $topic);
        $this->addUnreadPost($topic);

        $response = $this->actingAs($this->user)->get('/leap');

        $response->assertRedirect(
            action([SectionController::class, 'show'], ['section' => $section->id])
        );
    }

    #[Test]
    public function flash_message_is_success_and_contains_the_unread_topic_title(): void
    {
        $section = $this->makeSection();
        $topic = $this->makeTopic($section);
        ViewHelper::subscribeToTopic($this->user, $topic);
        $this->addUnreadPost($topic);

        $response = $this->actingAs($this->user)->get('/leap');

        $response->assertSessionHas('headerAlert', function ($alert) use ($topic) {
            return $alert['level'] === 'success'
                && str_contains($alert['body'], $topic->title);
        });
    }

    #[Test]
    public function does_not_leap_to_an_unsubscribed_topic(): void
    {
        $section = $this->makeSection();
        $topic = $this->makeTopic($section);
        ViewHelper::unsubscribeFromTopic($this->user, $topic);
        $this->addUnreadPost($topic);

        $response = $this->actingAs($this->user)->get('/leap');

        $response->assertRedirect(
            action([SectionController::class, 'show'], ['section' => $this->home->id])
        );
    }

    #[Test]
    public function does_not_leap_when_subscribed_topic_has_only_undated_posts(): void
    {
        $section = $this->makeSection();
        $topic = $this->makeTopic($section);
        ViewHelper::subscribeToTopic($this->user, $topic);
        Post::factory()->for($topic, 'topic')->for($this->user, 'author')->create(['time' => null]);

        $response = $this->actingAs($this->user)->get('/leap');

        $response->assertRedirect(
            action([SectionController::class, 'show'], ['section' => $this->home->id])
        );
    }

    #[Test]
    public function skips_read_topics_and_leaps_to_the_unread_one(): void
    {
        $section = $this->makeSection();
        $readTopic = $this->makeTopic($section);
        $unreadTopic = $this->makeTopic($section);

        $this->addUnreadPost($readTopic);
        $this->subscribeAndRead($readTopic);

        ViewHelper::subscribeToTopic($this->user, $unreadTopic);
        $this->addUnreadPost($unreadTopic);

        $response = $this->actingAs($this->user)->get('/leap');

        $response->assertSessionHas('headerAlert', function ($alert) use ($unreadTopic) {
            return str_contains($alert['body'], $unreadTopic->title);
        });
    }

    // This test verifies that when multiple subscribed topics have new posts,
    // leap surfaces the most recently updated one. It will FAIL until the
    // orderByDesc fix is applied — without ORDER BY, DB insertion order wins,
    // returning the older topic first.
    #[Test]
    public function leaps_to_the_most_recently_updated_topic_when_multiple_are_unread(): void
    {
        $section = $this->makeSection();

        // Subscribe to olderTopic first so its View record has a lower ID.
        // Without ORDER BY the query returns it first — the wrong result.
        $olderTopic = $this->makeTopic($section);
        $newerTopic = $this->makeTopic($section);

        ViewHelper::subscribeToTopic($this->user, $olderTopic);
        ViewHelper::subscribeToTopic($this->user, $newerTopic);

        // Both post times differ from the subscription's latest_view_date (≈ now),
        // so both topics are unread. newerTopic has the more recent post time.
        Post::factory()->for($olderTopic, 'topic')->for($this->user, 'author')
            ->create(['time' => now()->subDay()]);

        Post::factory()->for($newerTopic, 'topic')->for($this->user, 'author')
            ->create(['time' => $newerTopic->created_at->addHour()]);

        $response = $this->actingAs($this->user)->get('/leap');

        $response->assertSessionHas('headerAlert', function ($alert) use ($newerTopic) {
            return str_contains($alert['body'], $newerTopic->title);
        });
    }
}

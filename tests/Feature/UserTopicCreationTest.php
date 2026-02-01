<?php

namespace Tests\Feature;

use App\Models\Post;
use App\Models\Section;
use App\Models\Topic;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class UserTopicCreationTest extends TestCase
{
    use RefreshDatabase;

    protected User $moderator;

    protected User $normalUser;

    protected Section $home;

    protected Section $section;

    protected function setUp(): void
    {
        parent::setUp();

        $this->moderator = User::factory()->create();
        $this->normalUser = User::factory()->create();

        $this->home = Section::factory()
            ->for($this->moderator, 'moderator')
            ->create(['parent_id' => null]);

        $this->section = Section::factory()
            ->for($this->moderator, 'moderator')
            ->for($this->home, 'parent')
            ->create(['allow_user_topics' => false]);
    }

    #[Test]
    public function moderator_can_always_create_topics(): void
    {
        $this->assertTrue($this->moderator->can('create', [Topic::class, $this->section]));
    }

    #[Test]
    public function regular_user_cannot_create_topics_by_default(): void
    {
        $this->assertFalse($this->normalUser->can('create', [Topic::class, $this->section]));
    }

    #[Test]
    public function regular_user_can_create_topics_when_section_allows(): void
    {
        $this->section->update(['allow_user_topics' => true]);

        $this->assertTrue($this->normalUser->can('create', [Topic::class, $this->section]));
    }

    #[Test]
    public function moderator_can_toggle_allow_user_topics_setting(): void
    {
        $response = $this->actingAs($this->moderator)
            ->patch(route('section.update', $this->section), [
                'id' => $this->section->id,
                'form' => [
                    "section{$this->section->id}" => [
                        'id' => $this->section->id,
                        'title' => $this->section->title,
                        'intro' => $this->section->intro,
                        'parent_id' => $this->section->parent_id,
                        'user_id' => $this->section->user_id,
                        'weight' => $this->section->weight ?? 0,
                        'allow_user_topics' => 1,
                    ],
                ],
            ]);

        $response->assertRedirect(route('section.show', $this->section));

        $this->section->refresh();
        $this->assertTrue($this->section->allow_user_topics);
    }

    #[Test]
    public function normal_user_can_create_topic_when_allowed(): void
    {
        $this->section->update(['allow_user_topics' => true]);

        $this->actingAs($this->normalUser)
            ->post(route('topic.store'), [
                'title' => 'My New Topic',
                'intro' => 'This is my topic introduction',
                'section_id' => $this->section->id,
                'secret' => 0,
                'readonly' => 0,
            ]);

        $topic = Topic::where('title', 'My New Topic')->first();
        $this->assertNotNull($topic);
        $this->assertEquals($this->section->id, $topic->section_id);

        // Verify an initial post was created
        $post = Post::where('topic_id', $topic->id)->first();
        $this->assertNotNull($post);
        $this->assertEquals('My New Topic', $post->title);
        $this->assertEquals('This is my topic introduction', $post->text);
        $this->assertEquals($this->normalUser->id, $post->user_id);
    }

    #[Test]
    public function normal_user_cannot_bypass_restrictions_via_post(): void
    {
        $this->section->update(['allow_user_topics' => true]);

        // Attempt to create topic with restricted options via direct POST
        $this->actingAs($this->normalUser)
            ->post(route('topic.store'), [
                'title' => 'Hacked Topic',
                'intro' => 'Trying to bypass',
                'section_id' => $this->section->id,
                'secret' => 1,
                'readonly' => 1,
                'weight' => 5,
            ]);

        // Topic should be created but with forced default values
        $topic = Topic::where('title', 'Hacked Topic')->first();
        $this->assertNotNull($topic);
        $this->assertEquals(0, $topic->secret);
        $this->assertEquals(0, $topic->readonly);
        $this->assertEquals(0, $topic->weight);
    }

    #[Test]
    public function moderator_can_set_secret_readonly_and_weight(): void
    {
        $this->actingAs($this->moderator)
            ->post(route('topic.store'), [
                'title' => 'Moderator Topic',
                'intro' => 'Created by moderator',
                'section_id' => $this->section->id,
                'secret' => 1,
                'readonly' => 1,
                'weight' => 5,
            ]);

        $topic = Topic::where('title', 'Moderator Topic')->first();
        $this->assertNotNull($topic);
        $this->assertEquals(1, $topic->secret);
        $this->assertEquals(1, $topic->readonly);
        $this->assertEquals(5, $topic->weight);

        // Verify NO initial post was created for moderator
        $post = Post::where('topic_id', $topic->id)->first();
        $this->assertNull($post);
    }
}

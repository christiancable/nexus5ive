<?php

namespace Tests\Feature\Policies;

use App\Models\Post;
use App\Models\Section;
use App\Models\Topic;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Config;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class PostPolicyTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    private User $moderator;

    private User $author;

    private User $unrelatedUser;

    private Topic $topic;

    private Topic $readonlyTopic;

    private Post $post;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->forTheme()->create(['administrator' => true]);
        $this->moderator = User::factory()->forTheme()->create();
        $this->author = User::factory()->forTheme()->create();
        $this->unrelatedUser = User::factory()->forTheme()->create();

        $home = Section::factory()->for($this->admin, 'moderator')->create(['parent_id' => null]);
        $section = Section::factory()->for($this->moderator, 'moderator')->for($home, 'parent')->create();
        $this->topic = Topic::factory()->for($section)->create(['readonly' => false]);
        $this->readonlyTopic = Topic::factory()->for($section)->create(['readonly' => true]);
        $this->post = Post::factory()->for($this->topic)->for($this->author, 'author')->create(['time' => now()]);

        Config::set('nexus.recent_edit', 300);
    }

    // create

    #[Test]
    public function guest_cannot_create_post(): void
    {
        $guest = User::factory()->forTheme()->create(['is_guest' => true]);

        $this->assertFalse($guest->can('create', [Post::class, $this->topic]));
    }

    #[Test]
    public function admin_can_always_create_post(): void
    {
        $this->assertTrue($this->admin->can('create', [Post::class, $this->readonlyTopic]));
    }

    #[Test]
    public function moderator_can_create_post_in_readonly_topic(): void
    {
        $this->assertTrue($this->moderator->can('create', [Post::class, $this->readonlyTopic]));
    }

    #[Test]
    public function regular_user_can_create_post_in_open_topic(): void
    {
        $this->assertTrue($this->unrelatedUser->can('create', [Post::class, $this->topic]));
    }

    #[Test]
    public function regular_user_cannot_create_post_in_readonly_topic(): void
    {
        $this->assertFalse($this->unrelatedUser->can('create', [Post::class, $this->readonlyTopic]));
    }

    // update

    #[Test]
    public function admin_can_update_any_post(): void
    {
        $this->assertTrue($this->admin->can('update', $this->post));
    }

    #[Test]
    public function moderator_can_update_post_in_their_section(): void
    {
        $this->assertTrue($this->moderator->can('update', $this->post));
    }

    #[Test]
    public function author_can_update_their_recent_most_recent_post(): void
    {
        $this->assertTrue($this->author->can('update', $this->post));
    }

    #[Test]
    public function author_cannot_update_post_outside_time_limit(): void
    {
        $oldPost = Post::factory()->for($this->topic)->for($this->author, 'author')->create([
            'time' => now()->subSeconds(400),
        ]);

        $this->assertFalse($this->author->can('update', $oldPost));
    }

    #[Test]
    public function author_cannot_update_non_most_recent_post(): void
    {
        // Add a newer post so $this->post is no longer the latest
        Post::factory()->for($this->topic)->for($this->unrelatedUser, 'author')->create(['time' => now()->addSecond()]);

        $this->assertFalse($this->author->can('update', $this->post));
    }

    #[Test]
    public function unrelated_user_cannot_update_post(): void
    {
        $this->assertFalse($this->unrelatedUser->can('update', $this->post));
    }

    // delete

    #[Test]
    public function admin_can_delete_any_post(): void
    {
        $this->assertTrue($this->admin->can('delete', $this->post));
    }

    #[Test]
    public function moderator_can_delete_post_in_their_section(): void
    {
        $this->assertTrue($this->moderator->can('delete', $this->post));
    }

    #[Test]
    public function regular_user_cannot_delete_post(): void
    {
        $this->assertFalse($this->unrelatedUser->can('delete', $this->post));
    }
}

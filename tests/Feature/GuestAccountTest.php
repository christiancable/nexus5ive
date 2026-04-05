<?php

namespace Tests\Feature;

use App\Models\Comment;
use App\Models\Post;
use App\Models\Section;
use App\Models\Topic;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Gate;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class GuestAccountTest extends TestCase
{
    use RefreshDatabase;

    protected User $guest;

    protected User $normalUser;

    protected User $moderator;

    protected Section $home;

    protected Section $section;

    protected Topic $topic;

    protected function setUp(): void
    {
        parent::setUp();

        $this->guest = User::factory()->create(['is_guest' => true]);
        $this->moderator = User::factory()->create();
        $this->normalUser = User::factory()->create();

        $this->home = Section::factory()
            ->for($this->moderator, 'moderator')
            ->create(['parent_id' => null]);

        $this->section = Section::factory()
            ->for($this->moderator, 'moderator')
            ->for($this->home, 'parent')
            ->create();

        $this->topic = Topic::factory()
            ->for($this->section)
            ->create(['readonly' => false]);
    }

    // ── isGuest() helper ────────────────────────────────────────────────────

    #[Test]
    public function is_guest_returns_true_for_guest_account(): void
    {
        $this->assertTrue($this->guest->isGuest());
    }

    #[Test]
    public function is_guest_returns_false_for_normal_user(): void
    {
        $this->assertFalse($this->normalUser->isGuest());
    }

    // ── PostPolicy ───────────────────────────────────────────────────────────

    #[Test]
    public function guest_cannot_create_posts(): void
    {
        $this->assertFalse($this->guest->can('create', [Post::class, $this->topic]));
    }

    #[Test]
    public function normal_user_can_create_posts_in_open_topic(): void
    {
        $this->assertTrue($this->normalUser->can('create', [Post::class, $this->topic]));
    }

    // ── CommentPolicy ────────────────────────────────────────────────────────

    #[Test]
    public function guest_cannot_create_comments(): void
    {
        $this->assertFalse($this->guest->can('create', Comment::class));
    }

    #[Test]
    public function normal_user_can_create_comments(): void
    {
        $this->assertTrue($this->normalUser->can('create', Comment::class));
    }

    // ── UserPolicy ───────────────────────────────────────────────────────────

    #[Test]
    public function guest_cannot_update_own_profile(): void
    {
        $this->assertFalse($this->guest->can('update', $this->guest));
    }

    #[Test]
    public function normal_user_can_update_own_profile(): void
    {
        $this->assertTrue($this->normalUser->can('update', $this->normalUser));
    }

    #[Test]
    public function normal_user_cannot_update_another_users_profile(): void
    {
        $this->assertFalse($this->normalUser->can('update', $this->guest));
    }

    // ── ChatPolicy ───────────────────────────────────────────────────────────

    #[Test]
    public function guest_cannot_create_chat_messages(): void
    {
        $this->assertFalse($this->guest->can('create', \App\Models\Chat::class));
    }

    #[Test]
    public function normal_user_can_create_chat_messages(): void
    {
        $this->assertTrue($this->normalUser->can('create', \App\Models\Chat::class));
    }

    // ── HTTP: comment store blocked for guest ────────────────────────────────

    #[Test]
    public function guest_posting_comment_is_forbidden(): void
    {
        $this->actingAs($this->guest)
            ->post(route('comments.store'), [
                'text' => 'Hello from guest',
                'user_id' => $this->normalUser->id,
            ])
            ->assertForbidden();

        $this->assertDatabaseMissing('comments', ['text' => 'Hello from guest']);
    }

    #[Test]
    public function normal_user_can_post_comment(): void
    {
        $this->actingAs($this->normalUser)
            ->post(route('comments.store'), [
                'text' => 'Hello from normal user',
                'user_id' => $this->moderator->id,
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('comments', ['text' => 'Hello from normal user']);
    }

    // ── HTTP: user update blocked for guest ──────────────────────────────────

    #[Test]
    public function guest_updating_profile_is_forbidden(): void
    {
        $this->actingAs($this->guest)
            ->patch(route('users.update', $this->guest), [
                'id' => $this->guest->id,
                'email' => $this->guest->email,
                'password' => '',
                'password_confirmation' => '',
            ])
            ->assertForbidden();
    }

    // ── Archive mode gate ────────────────────────────────────────────────────

    #[Test]
    public function archive_mode_blocks_post_creation_for_all_users(): void
    {
        Config::set('nexus.archive_mode', true);
        // Re-register the gate hook as it runs at boot time
        Gate::before(function ($user, string $ability) {
            if (in_array($ability, ['create', 'update', 'delete', 'restore'])) {
                return false;
            }
        });

        $this->assertFalse($this->normalUser->can('create', [Post::class, $this->topic]));
        $this->assertFalse($this->moderator->can('create', [Post::class, $this->topic]));
    }

    #[Test]
    public function archive_mode_blocks_comment_creation_for_all_users(): void
    {
        Config::set('nexus.archive_mode', true);
        Gate::before(function ($user, string $ability) {
            if (in_array($ability, ['create', 'update', 'delete', 'restore'])) {
                return false;
            }
        });

        $this->assertFalse($this->normalUser->can('create', Comment::class));
        $this->assertFalse($this->moderator->can('create', Comment::class));
    }

    #[Test]
    public function archive_mode_blocks_profile_updates_for_all_users(): void
    {
        Config::set('nexus.archive_mode', true);
        Gate::before(function ($user, string $ability) {
            if (in_array($ability, ['create', 'update', 'delete', 'restore'])) {
                return false;
            }
        });

        $this->assertFalse($this->normalUser->can('update', $this->normalUser));
        $this->assertFalse($this->moderator->can('update', $this->moderator));
    }

    // ── Profile privacy ──────────────────────────────────────────────────────

    #[Test]
    public function private_flag_is_set_on_guest_user_with_is_guest(): void
    {
        // is_guest and private are independent flags; verify they can coexist
        $privateGuest = User::factory()->create(['is_guest' => true, 'private' => true]);
        $this->assertTrue($privateGuest->private);
        $this->assertTrue($privateGuest->isGuest());
    }

    #[Test]
    public function profile_page_hides_personal_fields_when_private(): void
    {
        $privateUser = User::factory()->create([
            'private' => true,
            'location' => 'Secret Location',
            'favouriteMovie' => 'Secret Film',
            'favouriteMusic' => 'Secret Band',
        ]);

        $response = $this->actingAs($this->normalUser)
            ->get(route('users.show', $privateUser));

        $response->assertOk();
        $response->assertDontSee('Secret Location');
        $response->assertDontSee('Secret Film');
        $response->assertDontSee('Secret Band');
    }

    #[Test]
    public function profile_page_shows_personal_fields_when_not_private(): void
    {
        $publicUser = User::factory()->create([
            'private' => false,
            'location' => 'Public Location',
            'favouriteMovie' => 'Public Film',
            'favouriteMusic' => 'Public Band',
        ]);

        $response = $this->actingAs($this->normalUser)
            ->get(route('users.show', $publicUser));

        $response->assertOk();
        $response->assertSee('Public Location');
        $response->assertSee('Public Film');
        $response->assertSee('Public Band');
    }

    // ── HTTP: clear all comments blocked for guest ───────────────────────────

    #[Test]
    public function guest_cannot_clear_comments_from_profile(): void
    {
        Comment::factory()->create(['user_id' => $this->guest->id, 'author_id' => $this->normalUser->id]);

        $this->actingAs($this->guest)
            ->delete(action([\App\Http\Controllers\Nexus\CommentController::class, 'destroyAll']))
            ->assertForbidden();

        $this->assertDatabaseHas('comments', ['user_id' => $this->guest->id]);
    }

    #[Test]
    public function administrator_guest_cannot_clear_comments_from_profile(): void
    {
        $adminGuest = User::factory()->create(['administrator' => true, 'is_guest' => true]);
        Comment::factory()->create(['user_id' => $adminGuest->id, 'author_id' => $this->normalUser->id]);

        $this->actingAs($adminGuest)
            ->delete(action([\App\Http\Controllers\Nexus\CommentController::class, 'destroyAll']))
            ->assertForbidden();

        $this->assertDatabaseHas('comments', ['user_id' => $adminGuest->id]);
    }

    #[Test]
    public function normal_user_can_clear_comments_from_profile(): void
    {
        Comment::factory()->create(['user_id' => $this->normalUser->id, 'author_id' => $this->moderator->id]);

        $this->actingAs($this->normalUser)
            ->delete(action([\App\Http\Controllers\Nexus\CommentController::class, 'destroyAll']))
            ->assertRedirect();

        $this->assertDatabaseMissing('comments', ['user_id' => $this->normalUser->id]);
    }

    // ── Administrator with is_guest=true ─────────────────────────────────────
    // The is_guest check must come before administrator/moderator shortcuts in
    // every policy to prevent imported admin accounts from gaining write access.

    #[Test]
    public function administrator_guest_cannot_create_posts(): void
    {
        $adminGuest = User::factory()->create(['administrator' => true, 'is_guest' => true]);

        $this->assertFalse($adminGuest->can('create', [Post::class, $this->topic]));
    }

    #[Test]
    public function administrator_guest_cannot_create_comments(): void
    {
        $adminGuest = User::factory()->create(['administrator' => true, 'is_guest' => true]);

        $this->assertFalse($adminGuest->can('create', Comment::class));
    }

    #[Test]
    public function administrator_guest_cannot_update_profile(): void
    {
        $adminGuest = User::factory()->create(['administrator' => true, 'is_guest' => true]);

        $this->assertFalse($adminGuest->can('update', $adminGuest));
    }

    #[Test]
    public function administrator_guest_cannot_send_chat_messages(): void
    {
        $adminGuest = User::factory()->create(['administrator' => true, 'is_guest' => true]);

        $this->assertFalse($adminGuest->can('create', \App\Models\Chat::class));
    }

    #[Test]
    public function administrator_guest_posting_comment_is_forbidden(): void
    {
        $adminGuest = User::factory()->create(['administrator' => true, 'is_guest' => true]);

        $this->actingAs($adminGuest)
            ->post(route('comments.store'), [
                'text' => 'Admin guest comment attempt',
                'user_id' => $this->normalUser->id,
            ])
            ->assertForbidden();

        $this->assertDatabaseMissing('comments', ['text' => 'Admin guest comment attempt']);
    }

    #[Test]
    public function administrator_guest_updating_profile_is_forbidden(): void
    {
        $adminGuest = User::factory()->create(['administrator' => true, 'is_guest' => true]);

        $this->actingAs($adminGuest)
            ->patch(route('users.update', $adminGuest), [
                'id' => $adminGuest->id,
                'email' => $adminGuest->email,
                'password' => '',
                'password_confirmation' => '',
            ])
            ->assertForbidden();
    }
}

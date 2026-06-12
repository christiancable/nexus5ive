<?php

namespace Tests\Feature;

use App\Models\Post;
use App\Models\Section;
use App\Models\Topic;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Config;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class PostControllerTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    private User $moderator;

    private User $author;

    private User $unrelatedUser;

    private Topic $topic;

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
        $this->topic = Topic::factory()->for($section)->create();
        $this->post = Post::factory()->for($this->topic)->for($this->author, 'author')->create(['time' => now()]);

        Config::set('nexus.recent_edit', 300);
    }

    private function updatePayload(Post $post, array $overrides = []): array
    {
        return array_merge([
            'id' => $post->id,
            'form' => [
                $post->id => [
                    'text' => 'Updated text',
                    'title' => 'Updated title',
                ],
            ],
        ], $overrides);
    }

    // update

    #[Test]
    public function admin_can_update_post(): void
    {
        $response = $this->actingAs($this->admin)
            ->put(route('posts.update', $this->post), $this->updatePayload($this->post));

        $response->assertRedirect();
        $this->assertDatabaseHas('posts', ['id' => $this->post->id, 'text' => 'Updated text']);
    }

    #[Test]
    public function moderator_can_update_post(): void
    {
        $response = $this->actingAs($this->moderator)
            ->put(route('posts.update', $this->post), $this->updatePayload($this->post));

        $response->assertRedirect();
        $this->assertDatabaseHas('posts', ['id' => $this->post->id, 'text' => 'Updated text']);
    }

    #[Test]
    public function author_can_update_their_recent_post(): void
    {
        $response = $this->actingAs($this->author)
            ->put(route('posts.update', $this->post), $this->updatePayload($this->post));

        $response->assertRedirect();
        $this->assertDatabaseHas('posts', ['id' => $this->post->id, 'text' => 'Updated text']);
    }

    #[Test]
    public function unrelated_user_cannot_update_post(): void
    {
        $response = $this->actingAs($this->unrelatedUser)
            ->put(route('posts.update', $this->post), $this->updatePayload($this->post));

        $response->assertStatus(403);
    }

    #[Test]
    public function update_requires_text(): void
    {
        $payload = $this->updatePayload($this->post);
        $payload['form'][$this->post->id]['text'] = '';

        $response = $this->actingAs($this->admin)
            ->put(route('posts.update', $this->post), $payload);

        $response->assertSessionHasErrorsIn('postUpdate'.$this->post->id, "form.{$this->post->id}.text");
    }

    #[Test]
    public function update_redirects_back_on_success(): void
    {
        $response = $this->actingAs($this->admin)
            ->put(route('posts.update', $this->post), $this->updatePayload($this->post));

        $response->assertRedirect();
    }

    // destroy

    #[Test]
    public function admin_can_delete_post(): void
    {
        $this->actingAs($this->admin)
            ->delete(route('posts.destroy', $this->post))
            ->assertRedirect(route('topic.show', $this->topic));

        $this->assertDatabaseMissing('posts', ['id' => $this->post->id]);
    }

    #[Test]
    public function moderator_can_delete_post(): void
    {
        $this->actingAs($this->moderator)
            ->delete(route('posts.destroy', $this->post));

        $this->assertDatabaseMissing('posts', ['id' => $this->post->id]);
    }

    #[Test]
    public function unrelated_user_cannot_delete_post(): void
    {
        $this->actingAs($this->unrelatedUser)
            ->delete(route('posts.destroy', $this->post))
            ->assertStatus(403);

        $this->assertDatabaseHas('posts', ['id' => $this->post->id]);
    }

    #[Test]
    public function destroy_redirects_to_topic(): void
    {
        $response = $this->actingAs($this->admin)
            ->delete(route('posts.destroy', $this->post));

        $response->assertRedirect(route('topic.show', ['topic' => $this->post->topic_id]));
    }

    // report

    #[Test]
    public function authenticated_user_can_view_report_page(): void
    {
        $this->actingAs($this->unrelatedUser)
            ->get(route('report', $this->post))
            ->assertOk();
    }

    #[Test]
    public function report_shows_anonymous_view_for_secret_topic(): void
    {
        $this->topic->secret = true;
        $this->topic->save();

        $response = $this->actingAs($this->unrelatedUser)
            ->get(route('report', $this->post));

        $response->assertOk();
        $response->assertViewHas('anonymous', true);
    }

    #[Test]
    public function report_not_anonymous_for_regular_topic(): void
    {
        $response = $this->actingAs($this->unrelatedUser)
            ->get(route('report', $this->post));

        $response->assertOk();
        $response->assertViewHas('anonymous', false);
    }

    #[Test]
    public function unauthenticated_user_cannot_view_report_page(): void
    {
        $this->get(route('report', $this->post))
            ->assertRedirect(route('login'));
    }
}

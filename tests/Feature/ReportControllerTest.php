<?php

namespace Tests\Feature;

use App\Http\Controllers\Nexus\ChatController;
use App\Http\Controllers\Nexus\TopicController;
use App\Models\Chat;
use App\Models\Post;
use App\Models\Section;
use App\Models\Topic;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class ReportControllerTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
    }

    #[Test]
    public function reporting_a_post_redirects_to_its_topic(): void
    {
        $home = Section::factory()->for($this->user, 'moderator')->create(['parent_id' => null]);
        $section = Section::factory()->for($this->user, 'moderator')->for($home, 'parent')->create();
        $topic = Topic::factory()->for($section, 'section')->create();
        $post = Post::factory()->for($topic, 'topic')->for($this->user, 'author')->create();

        $response = $this->actingAs($this->user)->post("/report/post/{$post->id}", [
            'reason' => 'spam',
        ]);

        $response->assertRedirect(
            action([TopicController::class, 'show'], ['topic' => $topic->id])
        );
    }

    #[Test]
    public function reporting_a_chat_redirects_to_the_chat_index(): void
    {
        $partner = User::factory()->create();
        $chat = Chat::factory()->create(['owner_id' => $this->user->id, 'partner_id' => $partner->id]);

        $response = $this->actingAs($this->user)->post("/report/chat/{$chat->id}", [
            'reason' => 'harassment',
        ]);

        $response->assertRedirect(
            action([ChatController::class, 'index'])
        );
    }

    #[Test]
    public function reporting_an_invalid_type_returns_404(): void
    {
        $response = $this->actingAs($this->user)->post('/report/comment/1', [
            'reason' => 'spam',
        ]);

        $response->assertNotFound();
    }

    #[Test]
    public function reporting_creates_a_report_record(): void
    {
        $home = Section::factory()->for($this->user, 'moderator')->create(['parent_id' => null]);
        $section = Section::factory()->for($this->user, 'moderator')->for($home, 'parent')->create();
        $topic = Topic::factory()->for($section, 'section')->create();
        $post = Post::factory()->for($topic, 'topic')->for($this->user, 'author')->create();

        $this->actingAs($this->user)->post("/report/post/{$post->id}", [
            'reason' => 'spam',
            'details' => 'This is spam content.',
        ]);

        $this->assertDatabaseHas('reports', [
            'reportable_type' => Post::class,
            'reportable_id' => $post->id,
            'reason' => 'spam',
            'status' => 'new',
        ]);
    }
}

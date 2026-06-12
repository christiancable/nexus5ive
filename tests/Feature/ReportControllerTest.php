<?php

namespace Tests\Feature;

use App\Models\Post;
use App\Models\Report;
use App\Models\Section;
use App\Models\Topic;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class ReportControllerTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    private User $user;

    private Post $post;

    protected function setUp(): void
    {
        parent::setUp();

        $owner = User::factory()->forTheme()->create();
        $home = Section::factory()->for($owner, 'moderator')->create(['parent_id' => null]);
        $section = Section::factory()->for($owner, 'moderator')->for($home, 'parent')->create();
        $topic = Topic::factory()->for($section)->create();
        $this->post = Post::factory()->for($topic)->for($owner, 'author')->create();

        $this->admin = User::factory()->forTheme()->create(['administrator' => true]);
        $this->user = User::factory()->forTheme()->create();
    }

    #[Test]
    public function authenticated_user_can_submit_a_report(): void
    {
        $response = $this->actingAs($this->user)->post("/report/post/{$this->post->id}", [
            'reason' => 'spam',
            'details' => 'This is spam content.',
            'anonymous' => false,
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('reports', [
            'reportable_type' => Post::class,
            'reportable_id' => $this->post->id,
            'reason' => 'spam',
            'reporter_id' => $this->user->id,
            'status' => 'new',
        ]);
    }

    #[Test]
    public function user_can_submit_an_anonymous_report(): void
    {
        $this->actingAs($this->user)->post("/report/post/{$this->post->id}", [
            'reason' => 'harassment',
            'anonymous' => true,
        ]);

        $this->assertDatabaseHas('reports', [
            'reportable_id' => $this->post->id,
            'reporter_id' => null,
        ]);
    }

    #[Test]
    public function report_requires_a_reason(): void
    {
        $response = $this->actingAs($this->user)->post("/report/post/{$this->post->id}", [
            'reason' => '',
        ]);

        $response->assertSessionHasErrors('reason');
    }

    #[Test]
    public function admin_can_view_reports_index(): void
    {
        $this->actingAs($this->admin)->get(route('reports.index'))->assertSuccessful();
    }

    #[Test]
    public function non_admin_cannot_view_reports_index(): void
    {
        $this->actingAs($this->user)->get(route('reports.index'))->assertStatus(403);
    }

    #[Test]
    public function admin_can_update_report_status(): void
    {
        $report = Report::factory()->create([
            'reportable_type' => Post::class,
            'reportable_id' => $this->post->id,
            'reason' => 'spam',
            'status' => 'new',
        ]);

        $response = $this->actingAs($this->admin)->put(route('reports.update', $report), [
            'status' => 'closed',
        ]);

        $response->assertRedirect(route('reports.index'));
        $this->assertDatabaseHas('reports', ['id' => $report->id, 'status' => 'closed']);
    }

    #[Test]
    public function non_admin_cannot_update_report_status(): void
    {
        $report = Report::factory()->create([
            'reportable_type' => Post::class,
            'reportable_id' => $this->post->id,
            'reason' => 'spam',
            'status' => 'new',
        ]);

        $response = $this->actingAs($this->user)->put(route('reports.update', $report), [
            'status' => 'closed',
        ]);

        $response->assertStatus(403);
    }

    #[Test]
    public function update_rejects_invalid_status(): void
    {
        $report = Report::factory()->create([
            'reportable_type' => Post::class,
            'reportable_id' => $this->post->id,
            'reason' => 'spam',
            'status' => 'new',
        ]);

        $response = $this->actingAs($this->admin)->put(route('reports.update', $report), [
            'status' => 'not_a_valid_status',
        ]);

        $response->assertSessionHasErrors('status');
    }
}

<?php

namespace Tests\Feature\Models;

use App\Models\Post;
use App\Models\Report;
use App\Models\Section;
use App\Models\Topic;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class ReportModelTest extends TestCase
{
    use RefreshDatabase;

    private Post $post;

    protected function setUp(): void
    {
        parent::setUp();

        $owner = User::factory()->forTheme()->create();
        $home = Section::factory()->for($owner, 'moderator')->create(['parent_id' => null]);
        $section = Section::factory()->for($owner, 'moderator')->for($home, 'parent')->create();
        $topic = Topic::factory()->for($section)->create();
        $this->post = Post::factory()->for($topic)->for($owner, 'author')->create();
    }

    #[Test]
    public function open_scope_excludes_closed_reports(): void
    {
        Report::factory()->create(['reportable_type' => Post::class, 'reportable_id' => $this->post->id, 'reason' => 'spam', 'status' => 'new']);
        Report::factory()->create(['reportable_type' => Post::class, 'reportable_id' => $this->post->id, 'reason' => 'spam', 'status' => 'under_review']);
        Report::factory()->create(['reportable_type' => Post::class, 'reportable_id' => $this->post->id, 'reason' => 'spam', 'status' => 'closed']);

        $open = Report::open()->get();

        $this->assertCount(2, $open);
        $this->assertTrue($open->every(fn ($r) => $r->status !== 'closed'));
    }

    #[Test]
    public function closed_scope_returns_only_closed_reports(): void
    {
        Report::factory()->create(['reportable_type' => Post::class, 'reportable_id' => $this->post->id, 'reason' => 'spam', 'status' => 'new']);
        Report::factory()->create(['reportable_type' => Post::class, 'reportable_id' => $this->post->id, 'reason' => 'spam', 'status' => 'closed']);

        $closed = Report::closed()->get();

        $this->assertCount(1, $closed);
        $this->assertEquals('closed', $closed->first()->status);
    }

    private function reportWithStatus(string $status): Report
    {
        $report = new Report;
        $report->status = $status;

        return $report;
    }

    #[Test]
    public function status_badge_class_for_new(): void
    {
        $this->assertEquals('bg-warning text-dark', $this->reportWithStatus('new')->status_badge_class);
    }

    #[Test]
    public function status_badge_class_for_under_review(): void
    {
        $this->assertEquals('bg-info text-dark', $this->reportWithStatus('under_review')->status_badge_class);
    }

    #[Test]
    public function status_badge_class_for_closed(): void
    {
        $this->assertEquals('bg-dark', $this->reportWithStatus('closed')->status_badge_class);
    }

    #[Test]
    public function status_badge_class_returns_default_for_unknown_status(): void
    {
        $this->assertEquals('bg-light text-dark', $this->reportWithStatus('anything_else')->status_badge_class);
    }

    #[Test]
    public function status_label_returns_human_readable_label(): void
    {
        $this->assertEquals('New', $this->reportWithStatus('new')->status_label);
        $this->assertEquals('Under Review', $this->reportWithStatus('under_review')->status_label);
        $this->assertEquals('Closed', $this->reportWithStatus('closed')->status_label);
        $this->assertEquals('Unknown', $this->reportWithStatus('bogus')->status_label);
    }

    #[Test]
    public function reason_label_returns_human_readable_label(): void
    {
        $new = fn (string $reason) => tap(new Report, fn ($r) => $r->reason = $reason);

        $this->assertEquals('Spam', $new('spam')->reason_label);
        $this->assertEquals('Harassment', $new('harassment')->reason_label);
        $this->assertEquals('Unknown', $new('bogus')->reason_label);
    }

    #[Test]
    public function snapshot_text_returns_text_from_json(): void
    {
        $report = new Report;
        $report->setRawAttributes(['reported_content_snapshot' => json_encode(['text' => 'Hello world'])]);

        $this->assertEquals('Hello world', $report->snapshot_text);
    }

    #[Test]
    public function snapshot_text_returns_fallback_when_text_key_missing(): void
    {
        $report = new Report;
        $report->setRawAttributes(['reported_content_snapshot' => json_encode([])]);

        $this->assertEquals('No content', $report->snapshot_text);
    }

    #[Test]
    public function reportable_link_returns_url_for_post_reportable(): void
    {
        $report = Report::factory()->create([
            'reportable_type' => Post::class,
            'reportable_id' => $this->post->id,
            'reason' => 'spam',
            'status' => 'new',
        ]);

        $this->assertIsString($report->reportable_link);
        $this->assertStringContainsString((string) $this->post->topic_id, $report->reportable_link);
    }

    #[Test]
    public function reportable_link_returns_null_when_reportable_is_not_a_post(): void
    {
        // Create a report not morphed to a Post by using a non-existent/mismatched morph
        $report = new Report;
        $report->reportable_type = 'App\\Models\\Topic'; // not a Post
        $report->reportable_id = 0;

        $this->assertNull($report->reportable_link);
    }
}

<?php

namespace Tests\Feature;

use App\Models\Section;
use App\Models\Topic;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class RestoreControllerTest extends TestCase
{
    use RefreshDatabase;

    private User $moderator;

    private User $unrelatedUser;

    private Section $home;

    private Section $section;

    private Section $destination;

    protected function setUp(): void
    {
        parent::setUp();

        $this->moderator = User::factory()->forTheme()->create();
        $this->unrelatedUser = User::factory()->forTheme()->create();

        $this->home = Section::factory()->for($this->moderator, 'moderator')->create(['parent_id' => null]);
        $this->section = Section::factory()->for($this->moderator, 'moderator')->for($this->home, 'parent')->create();
        $this->destination = Section::factory()->for($this->moderator, 'moderator')->for($this->home, 'parent')->create();
    }

    // index

    #[Test]
    public function user_can_view_restore_index(): void
    {
        $this->actingAs($this->moderator)
            ->get(route('archive.index'))
            ->assertOk();
    }

    #[Test]
    public function unauthenticated_user_cannot_view_restore_index(): void
    {
        $this->get(route('archive.index'))
            ->assertRedirect(route('login'));
    }

    // restore section

    #[Test]
    public function moderator_can_restore_their_trashed_section(): void
    {
        $this->section->delete();

        $response = $this->actingAs($this->moderator)
            ->post(route('archive.section', $this->section->id), [
                'destination' => $this->destination->id,
            ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('sections', ['id' => $this->section->id, 'deleted_at' => null]);
    }

    #[Test]
    public function cannot_restore_section_without_permission(): void
    {
        $this->section->delete();

        $response = $this->actingAs($this->unrelatedUser)
            ->post(route('archive.section', $this->section->id), [
                'destination' => $this->destination->id,
            ]);

        $response->assertStatus(403);
        $this->assertSoftDeleted('sections', ['id' => $this->section->id]);
    }

    #[Test]
    public function restoring_section_moves_it_to_destination(): void
    {
        $this->section->delete();

        $this->actingAs($this->moderator)
            ->post(route('archive.section', $this->section->id), [
                'destination' => $this->destination->id,
            ]);

        $this->assertDatabaseHas('sections', [
            'id' => $this->section->id,
            'parent_id' => $this->destination->id,
            'deleted_at' => null,
        ]);
    }

    // restore topic

    #[Test]
    public function moderator_can_restore_their_trashed_topic(): void
    {
        $topic = Topic::factory()->for($this->section)->create();
        $topic->delete();

        $response = $this->actingAs($this->moderator)
            ->post(route('archive.topic', $topic->id), [
                'destination' => $this->destination->id,
            ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('topics', ['id' => $topic->id, 'deleted_at' => null]);
    }

    #[Test]
    public function cannot_restore_topic_without_permission(): void
    {
        $topic = Topic::factory()->for($this->section)->create();
        $topic->delete();

        $response = $this->actingAs($this->unrelatedUser)
            ->post(route('archive.topic', $topic->id), [
                'destination' => $this->destination->id,
            ]);

        $response->assertStatus(403);
        $this->assertSoftDeleted('topics', ['id' => $topic->id]);
    }

    #[Test]
    public function restoring_topic_moves_it_to_destination_section(): void
    {
        $topic = Topic::factory()->for($this->section)->create();
        $topic->delete();

        $this->actingAs($this->moderator)
            ->post(route('archive.topic', $topic->id), [
                'destination' => $this->destination->id,
            ]);

        $this->assertDatabaseHas('topics', [
            'id' => $topic->id,
            'section_id' => $this->destination->id,
            'deleted_at' => null,
        ]);
    }
}

<?php

namespace Tests\Unit;

use App\Models\Section;
use App\Models\Topic;
use App\Helpers\TreeHelper;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TreeTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_returns_an_array_of_destinations()
    {
        $user = User::factory()->create();
        $section = Section::factory()->create(['user_id' => $user->id, 'parent_id' => null]);
        $topic = Topic::factory()->create(['section_id' => $section->id]);

        $tree = TreeHelper::tree();

        $this->assertIsArray($tree);
        $this->assertCount(2, $tree);
        $this->assertEquals($section->title, $tree[0]['title']);
        $this->assertEquals($topic->title, $tree[1]['title']);
    }

    /** @test */
    public function it_can_rebuild_the_cache()
    {
        $this->expectNotToPerformAssertions();
        TreeHelper::rebuild();
    }

    /** @test */
    public function it_reflects_newly_added_topics_after_rebuild()
    {
        $user = User::factory()->create();
        $section = Section::factory()->create(['user_id' => $user->id, 'parent_id' => null]);

        // Initial state
        TreeHelper::rebuild();
        $tree = TreeHelper::tree();
        $this->assertCount(1, $tree); // Just the section

        // Add a topic and rebuild
        $topic = Topic::factory()->create(['section_id' => $section->id]);
        TreeHelper::rebuild();
        $tree = TreeHelper::tree();

        $this->assertCount(2, $tree);
        $this->assertEquals($topic->title, $tree[1]['title']);
    }

    /** @test */
    public function it_reflects_removed_topics_after_rebuild()
    {
        $user = User::factory()->create();
        $section = Section::factory()->create(['user_id' => $user->id, 'parent_id' => null]);
        $topic = Topic::factory()->create(['section_id' => $section->id]);

        // Initial state
        TreeHelper::rebuild();
        $tree = TreeHelper::tree();
        $this->assertCount(2, $tree);

        // Remove the topic and rebuild
        $topic->delete();
        TreeHelper::rebuild();
        $tree = TreeHelper::tree();

        $this->assertCount(1, $tree);
    }
}

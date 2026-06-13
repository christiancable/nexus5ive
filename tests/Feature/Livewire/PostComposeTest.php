<?php

namespace Tests\Feature\Livewire;

use App\Livewire\PostCompose;
use App\Models\Section;
use App\Models\Topic;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class PostComposeTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    private Topic $topic;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->forTheme()->create();
        $owner = User::factory()->forTheme()->create();
        $home = Section::factory()->for($owner, 'moderator')->create(['parent_id' => null]);
        $section = Section::factory()->for($owner, 'moderator')->for($home, 'parent')->create();
        $this->topic = Topic::factory()->for($section)->create();
    }

    #[Test]
    public function mount_without_reply_leaves_text_empty(): void
    {
        Livewire::actingAs($this->user)
            ->test(PostCompose::class, ['topic' => $this->topic])
            ->assertSet('text', '');
    }

    #[Test]
    public function mount_with_reply_prefills_text_with_quoted_content(): void
    {
        $reply = ['text' => 'original message', 'username' => 'alice'];

        $component = Livewire::actingAs($this->user)
            ->test(PostCompose::class, ['topic' => $this->topic, 'reply' => $reply]);

        $text = $component->get('text');
        $this->assertStringContainsString('> original message', $text);
        $this->assertStringContainsString('@alice', $text);
    }
}

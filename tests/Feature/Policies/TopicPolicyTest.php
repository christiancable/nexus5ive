<?php

namespace Tests\Feature\Policies;

use App\Models\Section;
use App\Models\Topic;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class TopicPolicyTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    private User $moderator;

    private User $unrelatedUser;

    private Section $section;

    private Topic $topic;

    private Topic $readonlyTopic;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->forTheme()->create(['administrator' => true]);
        $this->moderator = User::factory()->forTheme()->create();
        $this->unrelatedUser = User::factory()->forTheme()->create();

        $home = Section::factory()->for($this->admin, 'moderator')->create(['parent_id' => null]);
        $this->section = Section::factory()
            ->for($this->moderator, 'moderator')
            ->for($home, 'parent')
            ->create(['allow_user_topics' => false]);

        $this->topic = Topic::factory()->for($this->section)->create(['readonly' => false, 'secret' => false]);
        $this->readonlyTopic = Topic::factory()->for($this->section)->create(['readonly' => true, 'secret' => false]);
    }

    #[Test]
    public function admin_bypasses_all_policy_checks(): void
    {
        $destination = Section::factory()->for($this->admin, 'moderator')->for($this->section, 'parent')->create();

        $this->assertTrue($this->admin->can('viewDetails', $this->topic));
        $this->assertTrue($this->admin->can('create', [Topic::class, $this->section]));
        $this->assertTrue($this->admin->can('update', $this->topic));
        $this->assertTrue($this->admin->can('move', [$this->topic, $destination]));
        $this->assertTrue($this->admin->can('delete', $this->topic));
        $this->assertTrue($this->admin->can('reply', $this->readonlyTopic));
    }

    #[Test]
    public function viewDetails_returns_true_for_non_secret_topic(): void
    {
        $this->assertTrue($this->unrelatedUser->can('viewDetails', $this->topic));
    }

    #[Test]
    public function viewDetails_returns_true_for_moderator_of_secret_topic(): void
    {
        $this->topic->update(['secret' => true]);

        $this->assertTrue($this->moderator->can('viewDetails', $this->topic));
    }

    #[Test]
    public function viewDetails_returns_false_for_non_moderator_of_secret_topic(): void
    {
        $this->topic->update(['secret' => true]);

        $this->assertFalse($this->unrelatedUser->can('viewDetails', $this->topic));
    }

    #[Test]
    public function create_allows_section_moderator(): void
    {
        $this->assertTrue($this->moderator->can('create', [Topic::class, $this->section]));
    }

    #[Test]
    public function create_allows_user_when_section_permits_user_topics(): void
    {
        $this->section->update(['allow_user_topics' => true]);

        $this->assertTrue($this->unrelatedUser->can('create', [Topic::class, $this->section]));
    }

    #[Test]
    public function create_denies_regular_user_when_section_disallows_topics(): void
    {
        $this->assertFalse($this->unrelatedUser->can('create', [Topic::class, $this->section]));
    }

    #[Test]
    public function update_allows_section_moderator(): void
    {
        $this->assertTrue($this->moderator->can('update', $this->topic));
    }

    #[Test]
    public function update_denies_non_moderator(): void
    {
        $this->assertFalse($this->unrelatedUser->can('update', $this->topic));
    }

    #[Test]
    public function move_allows_moderator_of_both_sections(): void
    {
        $destination = Section::factory()->for($this->moderator, 'moderator')->for($this->section, 'parent')->create();

        $this->assertTrue($this->moderator->can('move', [$this->topic, $destination]));
    }

    #[Test]
    public function move_denies_when_not_moderating_destination(): void
    {
        $destination = Section::factory()->for($this->unrelatedUser, 'moderator')->for($this->section, 'parent')->create();

        $this->assertFalse($this->moderator->can('move', [$this->topic, $destination]));
    }

    #[Test]
    public function delete_allows_section_moderator(): void
    {
        $this->assertTrue($this->moderator->can('delete', $this->topic));
    }

    #[Test]
    public function delete_denies_non_moderator(): void
    {
        $this->assertFalse($this->unrelatedUser->can('delete', $this->topic));
    }

    #[Test]
    public function reply_allows_moderator_on_readonly_topic(): void
    {
        $this->assertTrue($this->moderator->can('reply', $this->readonlyTopic));
    }

    #[Test]
    public function reply_allows_regular_user_on_open_topic(): void
    {
        $this->assertTrue($this->unrelatedUser->can('reply', $this->topic));
    }

    #[Test]
    public function reply_denies_regular_user_on_readonly_topic(): void
    {
        $this->assertFalse($this->unrelatedUser->can('reply', $this->readonlyTopic));
    }
}

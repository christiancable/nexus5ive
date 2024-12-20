<?php

namespace Tests\Intergration\Helpers;

use App\Helpers\RestoreHelper;
use App\Helpers\ViewHelper;
use App\Models\Post;
use App\Models\Section;
use App\Models\Topic;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class RestoreHelperTest extends TestCase
{
    use RefreshDatabase;

    public $sysop;

    public $home;

    public function setUp(): void
    {
        parent::setUp();
        $this->sysop = User::factory()->create();
        $this->home = Section::factory()->for($this->sysop, 'moderator')->create(['parent_id' => null]);
    }

    #[Test]
    public function restoreTopicToSectionDoesRestoresTopicToSection(): void
    {
        // GIVEN I have a topic in a section and then that topic is deleted
        $topic = Topic::factory()
            ->for($this->home, 'section')
            ->create();

        $topic->delete();
        $this->assertTrue($topic->trashed());

        // WHEN the topic is restored to the section
        RestoreHelper::restoreTopicToSection($topic, $this->home);

        // THEN the topic is restored
        $this->assertFalse($topic->trashed());
    }

    #[Test]
    public function restoreTopicToSectionDoesRestoresTopicAndPosts(): void
    {
        // GIVEN I have a topic with posts in a section
        $topic = Topic::factory()
            ->for($this->home, 'section')
            ->create();

        Post::factory()
            ->for($this->sysop, 'author')
            ->for($topic, 'topic')
            ->count(20)->create();
        $topic_id = $topic->id;

        // AND a user reads that topic
        $user = User::factory()->create();
        ViewHelper::updateReadProgress($user, $topic);

        $postsCount = $topic->posts->count();
        $this->assertNotEquals($postsCount, 0);

        // WHEN that topic is deleted
        $topic->delete();

        // THEN the topic is trashed and it has no posts
        $this->assertTrue($topic->trashed());
        $this->assertEquals(Topic::withTrashed()->find($topic_id)->posts->count(), 0);

        // WHEN the topic is restored to the section
        RestoreHelper::restoreTopicToSection($topic, $this->home);

        // THE the posts and views are restored
        $this->assertEquals($topic->posts->count(), $postsCount);

        // AND the topic is restored
        $this->assertFalse($topic->trashed());
    }

    #[Test]
    public function restoreTopicToSectionDoesRestoresTopicAndViews(): void
    {
        // GIVEN I have a topic with posts in a section
        $topic = Topic::factory()
            ->for($this->home, 'section')
            ->create();

        Post::factory()->count(20)->for($this->sysop, 'author')->for($topic, 'topic')->create();
        $topic_id = $topic->id;

        // AND a user reads that topic
        $user = User::factory()->create();
        ViewHelper::updateReadProgress($user, $topic);

        $viewsCount = $topic->views->count();
        $this->assertNotEquals($viewsCount, 0);

        // WHEN that topic is deleted
        $topic->delete();

        // THEN the topic is trashed and it has no posts
        $this->assertTrue($topic->trashed());
        $this->assertEquals(Topic::withTrashed()->find($topic_id)->views->count(), 0);

        // WHEN the topic is restored to the section
        RestoreHelper::restoreTopicToSection($topic, $this->home);

        // THEN the posts and views are restored
        $this->assertEquals($topic->views->count(), $viewsCount);

        // AND the topic is restored
        $this->assertFalse($topic->trashed());
    }

    #[Test]
    public function restoreSectionToSectionDoesRestoreSection(): void
    {
        // GIVEN we have a section with topics
        $section = Section::factory()
            ->for($this->home, 'parent')
            ->for($this->sysop, 'moderator')
            ->create();
        $section_id = $section->id;

        $number_of_topics = 10;
        Topic::factory()->count($number_of_topics)->for($section, 'section')->create();

        // AND another section
        $anotherSection = Section::factory()
            ->for($this->home, 'parent')
            ->for($this->sysop, 'moderator')
            ->create();

        // WHEN we delete the section
        $section->delete();

        // THEN the section is trashed and it has no topics
        $this->assertTrue($section->trashed());
        $this->assertEquals(Section::withTrashed()->find($section_id)->topics->count(), 0);
        $this->assertEquals(Section::withTrashed()->find($section_id)->trashedTopics->count(), $number_of_topics);

        // WHEN the section is restored to another section
        RestoreHelper::restoreSectionToSection($section, $anotherSection);

        // THEN its parent is the other section
        $this->assertEquals($section->parent_id, $anotherSection->id);

        // AND the section is nolonger trashed
        $this->assertFalse($section->trashed());
        // AND the topics are also restored
        $this->assertEquals(Section::find($section_id)->topics->count(), $number_of_topics);
        $this->assertEquals(Section::find($section_id)->trashedTopics->count(), 0);
    }
}

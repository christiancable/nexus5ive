<?php

namespace Tests\Intergration\Helpers;

use App\User;
use App\Post;
use App\Topic;
use App\Section;
use Tests\TestCase;
use App\Helpers\ViewHelper;
use App\Helpers\RestoreHelper;
use Illuminate\Foundation\Testing\RefreshDatabase;

class RestoreHelperTest extends TestCase
{
    use RefreshDatabase;

    /**
    * @test
    */
    public function restoreTopicToSectionDoesRestoresTopicToSection()
    {
        // GIVEN I have a topic in a section and then that topic is deleted
        $moderator = User::factory()->create();
        $section = Section::factory()
            ->for($moderator, 'moderator')
            ->create(
                ['parent_id' => null]
            );

        $topic = Topic::factory()
            ->for($section, 'section')
            ->create();

        $topic->delete();
        $this->assertTrue($topic->trashed());

        // WHEN the topic is restored to the section
        RestoreHelper::restoreTopicToSection($topic, $section);

        // THEN the topic is restored
        $this->assertFalse($topic->trashed());
    }

    /**
    * @test
    */
    public function restoreTopicToSectionDoesRestoresTopicAndPosts()
    {
        // GIVEN I have a topic with posts in a section
        $moderator = User::factory()->create();
        $section = Section::factory()
            ->for($moderator, 'moderator')
            ->create(['parent_id' => null]);

        $topic = Topic::factory()
            ->for($section, 'section')
            ->create();

        Post::factory()
            ->for($moderator, 'author')
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
        RestoreHelper::restoreTopicToSection($topic, $section);

        // THE the posts and views are restored
        $this->assertEquals($topic->posts->count(), $postsCount);

        // AND the topic is restored
        $this->assertFalse($topic->trashed());
    }

    /**
    * @test
    */
    public function restoreTopicToSectionDoesRestoresTopicAndViews()
    {
        // GIVEN I have a topic with posts in a section
        $moderator = User::factory()->create();
        $section = Section::factory()
            ->for($moderator, 'moderator')
            ->create(['parent_id' => null]);

        $topic = Topic::factory()
            ->for($section, 'section')
            ->create();
        Post::factory()->count(20)->for($moderator, 'author')->for($topic, 'topic')->create();
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
        RestoreHelper::restoreTopicToSection($topic, $section);

        // THEN the posts and views are restored
        $this->assertEquals($topic->views->count(), $viewsCount);

        // AND the topic is restored
        $this->assertFalse($topic->trashed());
    }

    /**
    * @test
    */
    public function restoreSectionToSectionDoesRestoreSection()
    {
        // GIVEN we have a section with topics
        $moderator = User::factory()->create();
        $home = Section::factory()->for($moderator, 'moderator')->create(['parent_id' => null]);

        $section = Section::factory()
        ->for($home, 'parent')
        ->for($moderator, 'moderator')
        ->create();
        $section_id = $section->id;

        $number_of_topics = 10;
        Topic::factory()->count($number_of_topics)->for($section, 'section')->create();

        // AND another section
        $anotherSection = Section::factory()
        ->for($home, 'parent')
        ->for($moderator, 'moderator')
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

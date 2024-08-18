<?php

namespace Tests\Intergration\Models;

use App\User;
use App\Post;
use App\Topic;
use App\Section;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class SectionTest extends TestCase
{
    use RefreshDatabase;

    public $home;
    public $sysop;

    public function setUp(): void
    {
        parent::setUp();
        $this->sysop = User::factory()->create();
        $this->home = Section::factory()
            ->for($this->sysop, 'moderator')
            ->create(['parent_id' => null]);
    }

    /**
     * @test
     */
    public function deletingSectionSoftDeletesSectionAndOnlyThatOne()
    {
        // GIVEN we have a main menu with a subsection
        $section = Section::factory()
            ->for($this->home, 'parent')
            ->for($this->sysop, 'moderator')
            ->create();

        // AND some other sections
        Section::factory()
            ->count(2)
            ->for($this->home, 'parent')
            ->for($this->sysop, 'moderator')
            ->create();

        $sectionCount = Section::all()->count();

        // WHEN that particular section is deleted
        $section->delete();

        // THEN number of sections goes down by one
        $sectionCountAfterDeletion = Section::all()->count();
        $this->assertEquals($sectionCountAfterDeletion, $sectionCount - 1);

        // AND that particular section is soft deleted
        $this->assertTrue($section->trashed());
    }

    /**
     * @test
     */
    public function deletingSectionSoftDeletesItsTopics()
    {
        // GIVEN we have a section
        $section = Section::factory()
            ->for($this->sysop, 'moderator')
            ->for($this->home, 'parent')
            ->create();

        // AND that section has topics
        Topic::factory()
            ->for($section, 'section')
            ->create();
        $topicsInSectionCount = $section->topics->count();

        $topicCount = Topic::all()->count();

        // WHEN we delete that section
        $section->delete();

        // THEN the total number of topics is reduced by the number of topics
        // belonging to the original section
        $topicCountAfterDeletion = Topic::all()->count();
        $this->assertEquals($topicCount - $topicsInSectionCount, $topicCountAfterDeletion);

        // AND the count of topics for that section is now zero
        $this->assertEquals(Topic::where('section_id', $section->id)->count(), 0);

        // BUT that section has soft deleted topics with match the orignal count
        $this->assertEquals(Topic::withTrashed()->where('section_id', $section->id)->count(), $topicsInSectionCount);
    }

    /**
     * @test
     */
    public function deletingSectionSoftDeletesItsSubsections()
    {
        // GIVEN we have a section
        $section = Section::factory()
            ->for($this->sysop, 'moderator')
            ->for($this->home, 'parent')
            ->create();

        // WITH subsections
        Section::factory()
            ->count(6)
            ->for($section, 'parent')
            ->for($this->sysop, 'moderator')
            ->create();

        $subsectionCount = Section::where('parent_id', $section->id)->count();

        // when we delete that section
        $section->delete();

        // then section and subsections are soft deleted
        $this->assertTrue($section->trashed());

        // we have no subsections
        $this->assertEquals(Section::where('parent_id', $section->id)->count(), 0);

        // we have the right amount of soft deleted subsections
        $this->assertEquals(Section::withTrashed()->where('parent_id', $section->id)->count(), $subsectionCount);
    }

    /**
     * @test
     */
    public function latestPostIsNullWhenTheSectionHasNoTopics()
    {
        /*
        GIVEN a section with no topics
        */
        $section = Section::factory()
            ->for($this->home, 'parent')
            ->for($this->sysop, 'moderator')
            ->create();

        /*
        THEN the latest post for that section is null
        */

        $this->assertNull($section->most_recent_post);
    }

    /**
     * @test
     */
    public function latestPostIsNullWhenTheTopicsHaveNoPosts()
    {
        /*
        GIVEN a section with no topics
        */
        $section = Section::factory()
            ->for($this->home, 'parent')
            ->for($this->sysop, 'moderator')
            ->create();

        /*
        WHEN we add topics but no posts
        */
        Topic::factory()
            ->count(10)
            ->for($section, 'section')
            ->create();

        /*
        THEN the latest post for that section is null
        */

        $this->assertNull($section->most_recent_post);
    }

    /**
     * @test
     */
    public function latestPostReturnsMostRecentPostAsNewPostsAreAdded()
    {
        /*
        GIVEN a section with topics
        */

        $moderator = User::factory()->create();
        $section = Section::factory()
            ->for($moderator, 'moderator')
            ->create(['parent_id' => null]);

        $topic1 = Topic::factory()
            ->for($section, 'section')
            ->create();

        $topic2 = Topic::factory()
            ->for($section, 'section')
            ->create();

        /*
        WHEN a post is added to one of the topics
        */

        $post1 = Post::factory()
            ->for($moderator, 'author')
            ->for($topic1, 'topic')
            ->create();

        /*
        THEN the latest post for that section is that post
        */
        $this->assertEquals($post1->id, $section->most_recent_post->id);

        /*
        WHEN a second post is added to a topic in that section
        */
        $post2 = Post::factory()
            ->for($moderator, 'author')
            ->for($topic2, 'topic')
            ->create();

        /*
        THEN the latest post for that section becomes that second post
        */
        $this->assertEquals($post2->id, $section->most_recent_post->id);
    }

    /**
     * @test
     */
    public function latestPostReturnsMostRecentPostAsPostsAreRemoved()
    {
        /*
        GIVEN a section with topics, and a first and second post
        */

        $moderator = User::factory()->create();
        $section = Section::factory()
            ->for($moderator, 'moderator')
            ->create(['parent_id' => null]);

        $topic1 = Topic::factory()
            ->for($section, 'section')
            ->create();

        $topic2 = Topic::factory()
            ->for($section, 'section')
            ->create();

        $post1 = Post::factory()
            ->for($moderator, 'author')
            ->for($topic1, 'topic')
            ->create();

        $post2 = Post::factory()
            ->for($moderator, 'author')
            ->for($topic2, 'topic')
            ->create();

        // second is the latest
        $this->assertEquals($post2->id, $section->most_recent_post->id);

        /*
        WHEN the second post is removed
        */
        $post2->delete();

        /*
        THEN the latest post for that section becomes the first post
        */
        $this->assertEquals($post1->id, $section->most_recent_post->id);

        /*
        WHEN the first post is removed
        */
        $post1->delete();

        /*
        THEN the latest post for that section becomes null
        */
        $this->assertEquals(null, $section->most_recent_post);
    }

     /**
     * @test
     */
    public function latestPostReturnsNullWhenTopicWithPreviousLatestPostIsMovedToAnotherSection()
    {
        /*
        GIVEN a section with a topic with a post which is the latest post for that section
        */

        $moderator = User::factory()->create();

        $section = Section::factory()
            ->for($moderator, 'moderator')
            ->create(['parent_id' => null]);

        $topic1 = Topic::factory()
            ->for($section, 'section')
            ->create();

        $post1 = Post::factory()
            ->for($moderator, 'author')
            ->for($topic1, 'topic')
            ->create();

        // post1 is the latest post
        $this->assertEquals($post1->id, $section->most_recent_post->id);

        /*
        WHEN the topic is moved to another section
        */

        $section2 = Section::factory()
            ->for($moderator, 'moderator')
            ->create(['parent_id' => null]);

        $topic1->update([
            'section_id' => $section2->id
        ]);

        /*
        THEN the latest post for that section becomes null
        */
        $this->assertNull($section->most_recent_post);

        /*
        AND the latest post for the new section is the original post
        */
        $this->assertEquals($post1->id, $section2->most_recent_post->id);
    }
}

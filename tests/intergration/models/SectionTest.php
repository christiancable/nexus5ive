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

    /**
     * @test
     */
    public function deletingSectionSoftDeletesSectionAndOnlyThatOne()
    {
        $user = User::factory()->create();

        // GIVEN we have a main menu with a subsection
        $mainmenu = Section::factory()
            ->create([
                'parent_id' => null,
                'user_id' => $user->id,
                ]);
        $section = Section::factory()
            ->create([
                'parent_id' => $mainmenu->id,
                'user_id' => $user->id,
                ]);

        // AND some other sections
        Section::factory()
            ->create([
                'parent_id' => $mainmenu->id,
                'user_id' => $user->id,
                ]);

        $sectionCount = Section::all()->count();

        // WHEN that particular section is deleted
        $section->delete();

        // THEN number of sections goes down by one
        $sectionCountAfterDeletion = Section::all()->count();
        $this->assertEquals($sectionCountAfterDeletion, $sectionCount-1);

        // AND that particular section is soft deleted
        $this->assertTrue($section->trashed());
    }

    /**
     * @test
     */
    public function deletingSectionSoftDeletesItsTopics()
    {
        // GIVEN we have a user
        $user = User::factory()->create();

        // AND we have a section
        $section = Section::factory()
            ->create([
                'parent_id' => null,
                'user_id' => $user->id,
                ]);

        // AND that section has topics
        Topic::factory()->create(['section_id' => $section->id]);
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
        // given we have a user with a section and that sub section
         $user = User::factory()->create();

        // AND we have a section
        $section = Section::factory()
            ->create([
                'parent_id' => null,
                'user_id' => $user->id,
                ]);

        // with subsections
        Section::factory()
            ->count(6)
            ->create([
                'parent_id' => $section->id,
                'user_id' => $user->id,
                ]);

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

        $moderator = User::factory()->create();
        $section = Section::factory()->create([
                'parent_id' => null,
                'user_id' => $moderator->id
        ]);

        /*
        WHEN
        */

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

        $moderator = User::factory()->create();
        $section = Section::factory()->create([
                'parent_id' => null,
                'user_id' => $moderator->id
        ]);

        /*
        WHEN we add topics but no posts
        */

        Topic::factory()->count(10)->create([
            'section_id' => $section->id
        ]);

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
        $section = Section::factory()->create([
                'parent_id' => null,
                'user_id' => $moderator->id
        ]);

        $topic1 = Topic::factory()->create([
            'section_id' => $section->id
        ]);

        $topic2 = Topic::factory()->create([
            'section_id' => $section->id
        ]);

        /*
        WHEN a post is added to one of the topics
        */

        $post1 = Post::factory()->create([
            'topic_id' => $topic1->id
        ]);

        /*
        THEN the latest post for that section is that post
        */
        $this->assertEquals($post1->id, $section->most_recent_post->id);

        /*
        WHEN a second post is added to a topic in that section
        */
        $post2 = Post::factory()->create([
            'topic_id' => $topic2->id
        ]);

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
        $section = Section::factory()->create([
                'parent_id' => null,
                'user_id' => $moderator->id
        ]);
        $topic1 = Topic::factory()->create([
            'section_id' => $section->id
        ]);
        $topic2 = Topic::factory()->create([
            'section_id' => $section->id
        ]);

        $post1 = Post::factory()->create([
            'topic_id' => $topic1->id
        ]);
        $post2 = Post::factory()->create([
            'topic_id' => $topic2->id
        ]);
        
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
        $section = Section::factory()->create([
                'parent_id' => null,
                'user_id' => $moderator->id
        ]);
        
        $topic1 = Topic::factory()->create([
            'section_id' => $section->id
        ]);
        
        $post1 = Post::factory()->create([
            'topic_id' => $topic1->id
        ]);
        
        // post1 is the latest post
        $this->assertEquals($post1->id, $section->most_recent_post->id);

        /*
        WHEN the topic is moved to another section
        */

        $section2 = Section::factory()->create([
                'parent_id' => null,
                'user_id' => $moderator->id
        ]);
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

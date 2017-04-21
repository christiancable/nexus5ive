<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Nexus\User;
use Nexus\Topic;
use Nexus\Post;
use Nexus\Section;

class RestoreHelperTest extends TestCase
{
    use DatabaseTransactions;
    
 /*
        // note this will be protected at the http layer by a request
        
        test restoreTopicToSection restores a topic to an existing section
        test restoreTopicToSection restores a topic to another existing section
        test restoreTopicToSection restores a topic along with its posts and views
 */

    public function test_restoreTopicToSection_restores_a_topic_to_a_section()
    {
        // GIVEN I have a topic in a section
        // and then that topic is deleted
        $section = factory(Section::class, 1)->create();
        $topic = factory(Topic::class, 1)->create(['section_id' => $section->id]);
        $topic->delete();
        $this->assertTrue($topic->trashed());
        
        // WHEN the topic is restored to the section
        \Nexus\Helpers\RestoreHelper::restoreTopicToSection($topic, $section);

        // THEN the topic is restored
        $this->assertFalse($topic->trashed());
    }


    public function test_restoreTopicToSection_restores_a_topic_and_posts()
    {
        // GIVEN I have a topic with posts in a section
        $section = factory(Section::class, 1)->create();
        $topic = factory(Topic::class, 1)->create(['section_id' => $section->id]);
        factory(Post::class, 20)->create(['topic_id' => $topic->id]);
        $topic_id = $topic->id;

        // and a user reads that topic
        $user = factory(User::class, 1)->create();
        \Nexus\Helpers\ViewHelper::updateReadProgress($user, $topic);

        $postsCount = $topic->posts->count();
        $this->assertNotEquals($postsCount, 0);

        // and then that topic is deleted
        $topic->delete();
            
        // check topic deleted
        $this->assertTrue($topic->trashed());

        // and that we have no posts - need to use trashed here
        $this->assertEquals(Topic::withTrashed()->find($topic_id)->posts->count(), 0);
            
        // WHEN the topic is restored to the section
        \Nexus\Helpers\RestoreHelper::restoreTopicToSection($topic, $section);

        // check posts and views are restored
        $this->assertEquals($topic->posts->count(), $postsCount);

        // THEN the topic is restored
        $this->assertFalse($topic->trashed());
    }


    public function test_restoreTopicToSection_restores_a_topic_and_views()
    {
        // GIVEN I have a topic with posts in a section
        $section = factory(Section::class, 1)->create();
        $topic = factory(Topic::class, 1)->create(['section_id' => $section->id]);
        factory(Post::class, 20)->create(['topic_id' => $topic->id]);
        $topic_id = $topic->id;

        // and a user reads that topic
        $user = factory(User::class, 1)->create();
        \Nexus\Helpers\ViewHelper::updateReadProgress($user, $topic);

        $viewsCount = $topic->views->count();
        $this->assertNotEquals($viewsCount, 0);

        // and then that topic is deleted
        $topic->delete();

        // check topic deleted
        $this->assertTrue($topic->trashed());

        // and that we have no views - need to use trashed here
        $this->assertEquals(Topic::withTrashed()->find($topic_id)->views->count(), 0);
            
        // WHEN the topic is restored to the section
        \Nexus\Helpers\RestoreHelper::restoreTopicToSection($topic, $section);

        // check posts and views are restored
        $this->assertEquals($topic->views->count(), $viewsCount);

        // THEN the topic is restored
        $this->assertFalse($topic->trashed());
    }

    public function test_restoreSectionToSection_restores_a_section()
    {
        // given we have a section with topics
        $number_of_topics = 10;
        $section = factory(Section::class, 1)->create();
        $section_id = $section->id;
        factory(Topic::class, $number_of_topics)->create(['section_id' => $section_id]);

        // and we have another section
        $anotherSection = factory(Section::class, 1)->create();
                        
        // and we delete the section
        $section->delete();
        // the section is deleted
        $this->assertTrue($section->trashed());
        // and so are the topics
        $this->assertEquals(Section::withTrashed()->find($section_id)->topics->count(), 0);
        $this->assertEquals(Section::withTrashed()->find($section_id)->trashedTopics->count(), $number_of_topics);

        // when the section is restored to another section
        \Nexus\Helpers\RestoreHelper::restoreSectionToSection($section, $anotherSection);
            
        // then the section is not trashed
        $this->assertFalse($section->trashed());
        // and neither are the topics
        $this->assertEquals(Section::find($section_id)->topics->count(), $number_of_topics);
        $this->assertEquals(Section::find($section_id)->trashedTopics->count(), 0);
            
        // and its parent is the other section
        $this->assertEquals($section->parent_id, $anotherSection->id);
    }
}

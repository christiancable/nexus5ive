<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Nexus\User;
use Nexus\Section;
use Nexus\Topic;

class SectionTest extends TestCase
{
    use DatabaseTransactions;
        
    public function test_deleting_section_soft_deletes_section_and_only_that_one()
    {
        $user = factory(User::class, 1)->create();

        // GIVEN we have a main menu with a subsection
        $mainmenu = factory(Section::class, 1)
            ->create([
                'parent_id' => null,
                'user_id' => $user->id,
                ]);
        $section = factory(Section::class, 1)
            ->create([
                'parent_id' => $mainmenu->id,
                'user_id' => $user->id,
                ]);
        
        // AND some other sections
        factory(Section::class, 10)
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
    
    
    public function test_deleting_section_soft_deletes_its_topics()
    {
                
        // GIVEN we have a section
        $section = factory(Section::class, 1)->create();
        // AND that section has a number of topics
        factory(Topic::class, 10)->create(['section_id' => $section->id]);
        $topicsInSectionCount = $section->topics->count();
        
        // AND we have another section with some topics
        $anotherSection = factory(Section::class, 1)->create();
        factory(Topic::class, 10)->create(['section_id' => $anotherSection->id]);
        

        $topicCount = Topic::all()->count();
        
 
        // WHEN we delete that section
        $section->delete();
    
        // THEN the total number of topics is reduced by the number of topics
        // belonging to that section
        $topicCountAfterDeletion = Topic::all()->count();
        $this->assertEquals($topicCount - $topicsInSectionCount, $topicCountAfterDeletion);

        // AND those topics are all soft deleted
        // AND the other topics are not affected
        
    }
    /*
    test_deleting_section_soft_deletes_its_topics
    test_deleting_section_soft_deletes_its_subsections

    TopicTest
    test_deleting_topic_soft_deletes_its_posts
    
    */
}

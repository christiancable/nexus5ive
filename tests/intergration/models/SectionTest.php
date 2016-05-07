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
    

    /*
    test_deleting_section_soft_deletes_its_topics
    test_deleting_section_soft_deletes_its_subsections

    TopicTest
    test_deleting_topic_soft_deletes_its_posts
    
    */
}

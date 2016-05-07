<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Nexus\User;
use Nexus\Section;

class SectionTest extends TestCase
{
    use DatabaseTransactions;
        
    public function test_deleting_section_soft_deletes_section_and_only_that_one()
    {
        // GIVEN we have a section
        $section = factory(Section::class,1)->create();
        
        // AND some other sections
        factory(Section::class,10)->create();
        
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

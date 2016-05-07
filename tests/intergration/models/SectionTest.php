<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Nexus\User;

class SectionTest extends TestCase
{
    use DatabaseTransactions;
        
    public function test_deleting_section_soft_deletes_section()
    {
        // given
        // when
        // then
    
        $user = \factory(User::class,1)->create();
        print_r($user->toArray()); 
        $this->assertTrue(false);
    }
    
    /*
    test_deleting_section_soft_deletes_its_topics
    test_deleting_section_soft_deletes_its_subsections

    TopicTest
    test_deleting_topic_soft_deletes_its_posts
    
    */
}

<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\User;
use App\Section;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class SectionTest extends TestCase
{
    use RefreshDatabase;
    
    /**
    * @test
    */
    public function moderator_can_create_new_subsection()
    {
        
        /*
        given we have
        - main menu
        - admin
        - sub section
        - sub section moderator
        
        */
        
        $sysop = factory(User::class)->create();
        $home = factory(Section::class)->create(['parent_id' => null]);
        $home->moderator()->associate($sysop);
        
        $moderator = factory(User::class)->create();
        $section = factory(Section::class)->create();
        $section->parent()->associate($home);
        $section->moderator()->associate($moderator);

        // $this->expectException('Illuminate\Auth\AuthenticationException');

        // create a new section
        $newSection = factory(Section::class)->make(['parent_id' => $home->id]);
     
        // fix this mental
        $arrayToPost = [];
        $arrayToPost['form']['sectionCreate'] = $newSection->toArray();

        // dd($arrayToPost);
        $this->actingAs($moderator);
        $response = $this->post('/section', $arrayToPost);

        dd($this->errors());
        $response->assertSuccessful();
        // dd($something);

        // dd($something);

        // dd($something);
        // see the section exists
        // dd($newSection);
           
            
            //  // who moderates a section
            //  // when they visit the section
            //  // they can create a sub section
            //  // and they can see it
            // $this->assertFalse(true);
        }
        
        public function user_cannot_create_subsection()
        {
            
        }
        
        /**
        * A basic test example.
        *
        * @return void
        */
        public function testExample()
        {
            $this->assertTrue(true);
        }
    }
    
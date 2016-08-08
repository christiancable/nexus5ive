<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Nexus\User;
use Nexus\Topic;
use Nexus\Post;
use Nexus\Section;

class testMentions extends TestCase
{
//     use DatabaseTransactions;
    use DatabaseMigrations;
    
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testUserCanClearMentions()
    {
        /* given that we have
        a logged in user
        a topic in a section
        a post in the topic made by another user
        */
        
        $faker = \Faker\Factory::create();

        $user = factory(User::class, 1)->create();
        $author = factory(User::class, 1)->create();
        $section = factory(Section::class,1)
            ->create([
                'parent_id' => null,
                'user_id' => $user->id,
            ]);
        $topic = factory(Topic::class)
            ->create([
                'section_id' => $section->id,
            ]);
        $post = factory(Post::class, 1)
            ->create(
                ['topic_id' => $topic->id,
                'user_id' => $author->id,
                'time' => $faker->dateTimeThisMonth('-2 days')]
            );
            
        // the user should not see the clear all mentions link

        $this->actingAs($user)
            ->visit('/section/' . $section->id)
            ->dontSee('Clear all mentions');
                
        /* 
        when the user is mentioned in the topic by the other user
        */
        
        \Nexus\Helpers\MentionHelper::addMention($user, $post);

        /* then
        the user can see the 'clear all notifications menu' and 
        */

        $this->actingAs($user)
            ->visit('/section/' . $section->id)
            ->see('Clear all mentions');
        
        // when the user selects the 'clear all notifications menu
        
        $this->actingAs($user)
            ->click('Clear all mentions');
            
        
        // the clear all notification menu is not there
        $this->actingAs($user)
            ->visit('/section/' . $section->id)
            ->dontSee('Clear all mentions');
        
    }
}

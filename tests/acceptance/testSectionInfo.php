<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use App\User;
use App\Topic;
use App\Post;
use App\Section;

class testSectionInfo extends TestCase
{
    use DatabaseTransactions;
    
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testUserCanSeeLatestPostTopic()
    {
        /* GIVEN that we have

        a user
        a section
        a subsction with a number of topics
        no posts
        */
        $faker = \Faker\Factory::create();

        $user = factory(App\User::class)->create();
 
        $section = factory(App\Section::class)
        ->create([
            'parent_id' => null,
            'user_id' => $user->id,
            ]);

        $subSection = factory(App\Section::class)
        ->create([
            'parent_id' => $section->id,
            'user_id' => $user->id,
            ]);


        $topic1 = factory(Topic::class)
        ->create([
            'section_id' => $subSection->id,
            ]);

        $topic2 = factory(Topic::class)
        ->create([
            'section_id' => $subSection->id,
            ]);

        $topic3 = factory(Topic::class)
        ->create([
            'section_id' => $subSection->id,
            ]);

        // the user should not see the Latest Post notice for any section
        $this->actingAs($user)
            ->visit('/section/' . $section->id)
            ->dontSee('Latest Post in');

        /* WHEN
         a post is added to topic1 
        */

        /* THEN we should see
        a Latest Post notice for topic 1
        */

        $post = factory(App\Post::class)
        ->create(
            ['topic_id' => $topic1->id,
            'user_id' => $user->id,
            ]
        );

        $this->actingAs($user)
            ->visit('/section/' . $section->id)
            ->see("Latest Post in")
            ->see($topic1->title);

        /* THEN WHEN
        a post is added to topic2 we should
        see Latest Post notice
        see $topic2->title
        not see $topic1->title
        */

        $post = factory(App\Post::class)
        ->create(
            ['topic_id' => $topic2->id,
            'user_id' => $user->id,
            ]
        );

        $this->actingAs($user)
            ->visit('/section/' . $section->id)
            ->see("Latest Post in")
            ->see($topic2->title)
            ->dontSee($topic1->title);
    }
}

<?php

namespace Tests\Acceptance;

use DateTime;
use App\User;
use App\Post;
use App\Topic;
use App\Section;
use Faker\Factory;
use Tests\BrowserKitTestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class TestSubscriptions extends BrowserKitTestCase
{
    use RefreshDatabase;
    
    /**
     * @test
     */
    public function userCanMarkAllSubscribedTopicsAsRead()
    {
        $faker = Factory::create();

        /* given we have */
        // a user
        // another user
        // a topic with a post
        $user = factory(User::class)->create();
        $originalUserID = $user->id;

        $author = factory(User::class)->create();
        $section = factory(Section::class)
        ->create([
            'parent_id' => null,
            'user_id' => $user->id,
            ]);
        $topic = factory(Topic::class)
        ->create([
            'section_id' => $section->id,
            ]);
        $post = factory(Post::class)
        ->create(
            ['topic_id' => $topic->id,
            'user_id' => $author->id,
            'time' => $faker->dateTimeThisMonth('-1 days'),
            ]
        );
        
        // user reads topic
        $this->actingAs($user)
            ->visit('/topic/' . $topic->id);

        // a new post is added to the topic
        factory(Post::class)
            ->create(
                ['topic_id' => $topic->id,
                'user_id' => $author->id,
                'time' => new DateTime("now"),
                ]
            );

        /* WHEN */
        // user visits section and clicks Next
        $this->actingAs($user)
            ->visit('/section/' . $section->id)
            ->click('Next')
            ->see('People have been talking! New posts found in')
            ->dontSee('No updated topics found. Why not start a new conversation or read more sections?')
            ->click('mark all subscribed topics as read');
    
        /* THEN */
        // does not see the new posts found
         $this->actingAs($user)
            ->visit('/section/' . $section->id)
            ->click('Next')
            ->see('No updated topics found. Why not start a new conversation or read more sections?');


        sleep(1);
        // a new post is added to the topic
        factory(Post::class)
            ->create(
                ['topic_id' => $topic->id,
                'user_id' => $author->id,
                'time' => new DateTime("now"),
                ]
            );
        
        $this->actingAs($user)
            ->visit('/section/' . $section->id)
            ->click('Next')
            ->see('People have been talking! New posts found in');
            // ->dontSee('No updated topics found. Why not start a new conversation or read more sections?');
    }

    public function testUserCanUnsubscribeFromTopic()
    {
        /* GIVEN
        we have a user
        we have a section
        we have posts in that section
        */
        $faker = Factory::create();

        $user = factory(User::class)->create();
        $section = factory(Section::class)
        ->create([
            'parent_id' => null,
            'user_id' => $user->id,
            ]);
        $topic = factory(Topic::class)
        ->create([
        'section_id' => $section->id,
        ]);
        $post = factory(Post::class)
        ->create(
            ['topic_id' => $topic->id,
            'user_id' => $user->id,
            'time' => $faker->dateTimeThisMonth('-1 days'),
            ]
        );
     /* WHEN
        the user visits the topic
        and clicks Unsubscribe from this topic
        */

        $this->actingAs($user)
            ->visit('/topic/' . $topic->id)
            ->press('Unsubscribe from this topic');

        /* THEN
        the user visits the topic
        sees Subscribe to this topic */
        $this->actingAs($user)
            ->visit('/topic/' . $topic->id)
            ->dontSee('Unsubscribe from this topic')
            ->see('Subscribe to this topic');
    }
}

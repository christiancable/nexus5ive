<?php

namespace Tests\Acceptance;

use App\User;
use App\Post;
use App\Topic;
use App\Section;
use Faker\Factory;
use Tests\BrowserKitTestCase;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class TestMentions extends BrowserKitTestCase
{
    use DatabaseTransactions;
    
    /**
     * @test
     * @return void
     */
    public function userCanClearMentions()
    {
        /* GIVEN that we have
        a logged in user
        a topic in a section
        a post in the topic made by another user
        */
        
        $faker = Factory::create();

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
            ]
        );

        // the user should not see the clear all mentions link
        $this->actingAs($user)
            ->visit('/section/' . $section->id)
            ->dontSee('Clear All Mentions');

        // WHEN the user is mentioned in the topic by the other user
        // \App\Helpers\MentionHelper::addMention($user, $post);

        $user->addMention($post);
        
        // reloading the model here because otherwise the related
        $user = User::find($originalUserID);

        // THEN user now sees the 'clear all notifications menu'
        $this->actingAs($user)
            ->visit('/section/' . $section->id)
            ->see('Clear All Mentions');
        
        // WHEN the user selects the 'clear all notifications menu
        $this->actingAs($user)
            ->press('Clear All Mentions');

        // updating the test's view of $user
        $user = User::find($originalUserID);
        
        // THEN the user doesn't see the clear all mentions option
        $this->actingAs($user)
            ->visit('/section/' . $section->id)
            ->dontSee('Clear all Mentions');
    }
}
<?php

namespace Tests\Acceptance;

use App\User;
use App\Post;
use App\Topic;
use App\Section;
use Tests\BrowserKitTestCase;
use App\Helpers\NxCodeHelper;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class TestLatestPosts extends BrowserKitTestCase
{
    use DatabaseTransactions;

    /**
     * @test
     */
    public function userSeesPostPreviewForTopicWithPosts()
    {
        /*
        GIVEN we have a topic with posts
        WHEN we visit the latest posts page
        THEN should see the text of the latest post
        */

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
                ]
            );

        $previewText = substr(strip_tags(NxCodeHelper::nxDecode($post->text)), 0, 140);
        $this->actingAs($user)
            ->visit('/section/latest')
            ->see($previewText);
    }

    /**
     * @test
     */
    public function userDoesNotSeePostPreviewForUnsubscribedTopicWithPosts()
    {
         /*
        GIVEN we have a topic with posts
        WHEN the user unsubscribes from the topic
        AND we visit the latest posts page
        THEN we should see the text of the latest post
        */

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
                ]
            );

        $previewText = substr(strip_tags(NxCodeHelper::nxDecode($post->text)), 0, 140);

        $this->actingAs($user)
            ->visit('/topic/' . $topic->id)
            ->press('Unsubscribe from this topic');

        $this->actingAs($user)
            ->visit('/section/latest')
            ->dontSee($previewText);
    }
}

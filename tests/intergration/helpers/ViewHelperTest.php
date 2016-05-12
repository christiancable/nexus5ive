<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Nexus\User;
use Nexus\Topic;
use Nexus\Post;

class ViewHelperTest extends TestCase
{
	use DatabaseTransactions;
    
   public function test_getReadProgress_returns_time_of_most_recently_read_post_time()
    {
        $faker = \Faker\Factory::create();

        // GIVEN  we have a user
        $user = factory(User::class, 1)->create();

        // AND we have a topic with posts
        $topic = factory(Topic::class, 1)->create();
        factory(Post::class, 20)
            ->create(
                ['topic_id' => $topic->id,
                'time' => $faker->dateTimeThisMonth('-2 days')]
            );
            
        // AND the most recent post being from yesterday
        $newPost = factory(Post::class, 1)
            ->create(
                ['topic_id' => $topic->id,
                'time' => $faker->dateTimeThisMonth('-1 days')]
            );
          
        // WHEN the user reads the topic
        \Nexus\Helpers\ViewHelper::updateReadProgress($user, $topic);

        // THEN the date of the most recent post in the topic matches
        // the one most recently read by the user
        $this->assertEquals(
            $topic->most_recent_post_time,
            \Nexus\Helpers\ViewHelper::getReadProgress($user, $topic)
        );

        // WHEN a new post is added now
        $anotherPost = factory(Post::class, 1)
            ->create(
                ['topic_id' => $topic->id,
                'time' => new \DateTime('now')]
            );

        // THEN the date of the most recent post in the topic does not matches
        // the one most recently read by the user
        $this->assertNotEquals(
            $topic->most_recent_post_time,
            \Nexus\Helpers\ViewHelper::getReadProgress($user, $topic)
        );

    }
}

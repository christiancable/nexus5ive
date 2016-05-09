<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Nexus\Topic;
use Nexus\Post;

class TopicTest extends TestCase
{
	use DatabaseTransactions;
	
    public function test_deleting_topic_soft_deletes_its_posts()
    {
        // GIVEN we have a topic with post
        $topic = factory(Topic::class, 1)->create();
        factory(Post::class, 20)->create(['topic_id' => $topic->id]);
    
        // we have 1 topic with 20 posts
        $this->assertEquals(Topic::all()->count(), 1);
        $this->assertEquals(Post::where('topic_id', $topic->id)->count(), 20);
    
        // WHEN we archive the topic
        $topic->delete();

        // THEN we have no topics and no posts
        $this->assertEquals(Topic::all()->count(), 0);
        $this->assertEquals(Post::all()->count(), 0);

        // BUT we have 1 trashed topic and 20 trashed posts
        $this->assertEquals(Topic::withTrashed()->count(), 1);
        $this->assertEquals(Post::withTrashed()->where('topic_id', $topic->id)->count(), 20);
    }
}
 /*
    @todo

    test that getMostRecentPostTimeAttribute returns the time of the most recent post
    test that mostRecentlyReadPostDate returns the most recently read post for that user
    test that unreadPosts returns the posts which are unread by that user
 */

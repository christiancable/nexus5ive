<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class LatestTest extends TestCase {

    use DatabaseMigrations;

    /**
    * @test
    */
    public function a_user_can_reply_to_a_post() {
        /*
        given
            we have topics with posts
        when
            a user visits /latest
        then
            the user can post a reply to a post
        */
        
    }
}
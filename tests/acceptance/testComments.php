<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use App\User;
use App\Topic;
use App\Post;
use App\Section;
use App\Comment;

class testComments extends BrowserKitTestCase
{
    use DatabaseTransactions;

    /**
     * @test
     * @return void
     */
    public function userCanClearTheirOwnComments()
    {
        /* 
        GIVEN THAT WE HAVE
        - a logged in user
        -  with comments
        WHEN
        - user clicks 'clear comments' on their profile page
        THEN
        - comments for that user are cleared
        */

        $sysop = factory(User::class)->create();
        $user = factory(User::class)->create();
        $anotherUser = factory(App\User::class)->create();

        $home = factory(Section::class)
            ->create([
            'parent_id' => null,
            'user_id' => $user->id,
            ]);

        $comment = factory(Comment::class)
            ->create([
                'user_id' => $user->id,
                'author_id'=> $anotherUser->id,
            ]);
        
        // see comment
        $this->actingAs($user)
            ->visitRoute('users.show', $user->username)
            ->see($comment->text);

        // press the clear comments button
        $this->actingAs($user)
            ->visitRoute('users.show', $user->username)
            ->press('Clear All Comments');

        // dont see button 
        $this->actingAs($user)
            ->visitRoute('users.show', $user->username)
            ->dontSee('Clear All Comments');

        // don't see comments
        $this->actingAs($user)
            ->visitRoute('users.show', $user->username)
            ->dontSee($comment->text);

    }

}

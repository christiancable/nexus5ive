<?php

namespace Tests\Browser;

use App\User;
use App\Section;
use App\Comment;
use Tests\DuskTestCase;
use Laravel\Dusk\Browser;
use Illuminate\Support\Facades\Hash;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class CommentsTest extends DuskTestCase
{
    use DatabaseMigrations;

    protected $user;
    protected $user2;

    protected function setUp(): void
    {
        parent::setUp();

        // set up bbs with a sysop and main menu
        $sysop = User::factory()->create();
        $home = Section::factory()->create([
            'parent_id' => null,
            'user_id' => $sysop->id,
        ]);

        // add users for testing
        $this->user = User::factory()->create();
        $this->user2 = User::factory()->create();
    }

    /*
    * a given user can clear a comment from their profile page
    */
    public function testUserCanClearSingleComment()
    {
        $user = $this->user;

        $comment1 = Comment::factory()->create([
            'user_id' => $user->id,
            'author_id' => $this->user2->id,
        ]);

        $comment2 = Comment::factory()->create([
            'user_id' => $user->id,
            'author_id' => $this->user2->id,
        ]);

        $topDeleteButton = "button.btn-danger:first-of-type";

        $this->browse(function ($browser) use ($user, $comment1, $comment2, $topDeleteButton) {
            // GIVEN user has comments on their profile
            // TODO count comments
            $browser->loginAs($user)
                    ->visit('/users/' . $user->username)
                    ->assertSee($comment1->text)
                    ->assertSee($comment2->text);

            // WHEN user clicks clear delete button for top comment (comment2)
            $browser->loginAs($user)
                    ->visit('/users/' . $user->username)
                    ->press($topDeleteButton);


             $browser->loginAs($user)
                    ->visit('/users/' . $user->username)
            // THEN user can no longer see top comment
                    ->assertDontSee($comment2->text)
            // BUT user can still see other the other comment
                    ->assertSee($comment1->text);
        });
    }

    /*
    * a given user can clear all comments from their profile page
    */
    public function testUserCanClearAllComments()
    {
        $user = $this->user;

        $comment1 = Comment::factory()->create([
            'user_id' => $user->id,
            'author_id' => $this->user2->id,
        ]);

        $comment2 = Comment::factory()->create([
            'user_id' => $user->id,
            'author_id' => $this->user2->id,
        ]);

        $this->browse(function ($browser) use ($user, $comment1, $comment2) {

            // GIVEN user has comments on their profile
            $browser->loginAs($user)
                    ->visit('/users/' . $user->username)
                    ->assertSee($comment1->text)
                    ->assertSee($comment2->text);

            // WHEN user clicks clear comments
            $browser->loginAs($user)
                    ->visit('/users/' . $user->username)
                    ->press('Clear All Comments');

            // THEN user can no longer see any comments
             $browser->loginAs($user)
                    ->visit('/users/' . $user->username)
                    ->assertDontSee($comment1->text)
                    ->assertDontSee($comment2->text);
        });
    }
}

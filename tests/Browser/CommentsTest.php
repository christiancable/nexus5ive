<?php

namespace Tests\Browser;

use App\Models\Comment;
use App\Models\Section;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use PHPUnit\Framework\Attributes\Test;
use Tests\DuskTestCase;

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
    #[Test]
    public function test_user_can_clear_single_comment(): void
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

        $topDeleteButton = 'button.btn-danger:first-of-type';

        $this->browse(function ($browser) use ($user, $comment1, $comment2, $topDeleteButton) {
            // GIVEN user has comments on their profile
            // TODO count comments
            $browser->loginAs($user)
                ->visit('/users/'.$user->username)
                ->assertSee($comment1->text)
                ->assertSee($comment2->text);

            // WHEN user clicks clear delete button for top comment (comment2)
            $browser->loginAs($user)
                ->visit('/users/'.$user->username)
                ->press($topDeleteButton);

            $browser->loginAs($user)
                ->visit('/users/'.$user->username)
            // THEN user can no longer see top comment
                ->assertDontSee($comment2->text)
            // BUT user can still see other the other comment
                ->assertSee($comment1->text);
        });
    }

    /*
    * a given user can clear all comments from their profile page
    */
    #[Test]
    public function test_user_can_clear_all_comments(): void
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

            $browser->loginAs($user);
            // GIVEN user has comments on their profile
            $browser
                ->visit('/users/'.$user->username)
                ->assertSee($comment1->text)
                ->assertSee($comment2->text);

            // WHEN user clicks clear comments
            $browser
                ->waitFor('@btn-clear-all-comments')
                ->assertVisible('@btn-clear-all-comments')
                ->scrollIntoView('@btn-clear-all-comments')
                ->press('@btn-clear-all-comments');

            // THEN user can no longer see any comments
            $browser
                ->assertDontSee($comment1->text)
                ->assertDontSee($comment2->text);
        });
    }
}

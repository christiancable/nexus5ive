<?php

namespace Tests\Browser;

use App\Models\Section;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use PHPUnit\Framework\Attributes\Test;
use Tests\DuskTestCase;

class UsersTest extends DuskTestCase
{
    use DatabaseMigrations;

    private $sysop;

    private $moderator;

    protected function setUp(): void
    {
        parent::setUp();

        /* setup a bbs with a BBS with a sysop, and a section with a moderator */

        $this->sysop = User::factory()->create();
        $home = Section::factory()->for($this->sysop, 'moderator')->create([
            'parent_id' => null,
        ]);

        $this->moderator = User::factory()->create();
        $section = Section::factory()
            ->for($this->moderator, 'moderator')
            ->for($home, 'parent')
            ->create();

        // $this->user = User::factory()->create();
        // $this->user2 = User::factory()->create();
        // $this->user3 = User::factory()->create();
    }

    #[Test]
    public function userListContainsUsers()
    {

        // GIVEN we have a logged in user
        // WHEN they visit the users list
        // THEN they see users

        $user = User::factory()->create();

        $this->browse(
            function ($browser) use ($user) {
                $browser->loginAs($user)
                    ->visit('/users/')
                    ->assertSee($this->sysop->name)
                    ->assertSee($this->moderator->name)
                    ->assertSee($user->name);
            }
        );
    }

    #[Test]
    public function userListCanBeFiltered()
    {
        $name = 'Sir Professor Doctor Test';
        $user = User::factory()->create(['name' => $name]);

        $this->browse(
            function ($browser) use ($user, $name) {
                $browser->loginAs($user)
                    ->visit('/users/')
                    ->assertSee($this->sysop->name)
                    ->assertSee($this->moderator->name)
                    ->assertSee($user->name)

                    // WHEN we filter by the name of the user
                    ->type('@user-filter', $name)
                    ->pause(1000) // Allow time for the input to trigger the search
                    // THEN we see $user
                    ->assertSee($user->name)
                    // AND not the sysop or moderator
                    ->assertDontSee($this->sysop->name)
                    ->assertDontSee($this->moderator->name)

                    // WHEN we filter by text which will not be matched
                    ->type('@user-filter', 'this-is-unlikely-to-be-randomly-matched')
                    ->waitForText('No users found for ')
                    // THEN we see the no users found message
                    ->assertSee('No users found for ')

                    // WHEN we clear the filter
                    ->clear('@user-filter')
                    // Simulate typing an empty string to trigger the change
                    ->type('@user-filter', ' ') // Ensure the input is an empry string to trigger the js event change magic to happen due to how .live works
                    // Wait until the no users found message disappears
                    ->waitUntilMissingText('No users found for ')
                    // AND wait for user grid to be visible
                    ->waitFor('@user-grid')
                    // THEN we see all the users
                    ->assertSee($this->sysop->name)
                    ->assertSee($this->moderator->name)
                    ->assertSee($user->name);
            }
        );
    }
}

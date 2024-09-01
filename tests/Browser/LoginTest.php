<?php

namespace Tests\Browser;

use App\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Facades\Hash;
use Tests\DuskTestCase;

class LoginTest extends DuskTestCase
{
    use DatabaseMigrations;

    /**
     * A Dusk test example.
     */
    public function testBasicExample(): void
    {
        $user = User::factory()->create([
            'password' => Hash::make('password'),
        ]);

        $this->browse(function ($browser) use ($user) {
            $browser->visit('/')
                ->type('username', $user->username)
                ->type('password', 'password')
                ->press('Log In')
                ->assertPathIs('/');
        });
    }
}

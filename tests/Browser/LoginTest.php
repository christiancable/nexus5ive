<?php

namespace Tests\Browser;

use App\User;
use Tests\DuskTestCase;
use Laravel\Dusk\Browser;
use Illuminate\Support\Facades\Hash;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class LoginTest extends DuskTestCase
{
    use DatabaseMigrations;

    /**
     * A Dusk test example.
     *
     * @return void
     */
    public function testBasicExample()
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

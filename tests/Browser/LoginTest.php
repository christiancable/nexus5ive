<?php

namespace Tests\Browser;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Facades\Hash;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use Tests\DuskTestCase;

class LoginTest extends DuskTestCase
{
    use DatabaseMigrations;

    /**
     * SLOW (~26s): Form submission waits for full page load after login redirect.
     */
    #[Test]
    #[Group('slow')]
    public function testBasicExample(): void
    {
        $user = User::factory()->create([
            'password' => Hash::make('password'),
        ]);

        $this->browse(function ($browser) use ($user) {
            $browser->visit('/')
                ->type('username', $user->username)
                ->type('password', 'password')
                ->press('Log in')
                ->assertPathIs('/');
        });
    }
}

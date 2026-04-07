<?php

use App\Models\User;
use Illuminate\Support\Facades\Hash;

test('allows a user to log in', function () {
    $user = User::factory()->create([
        'password' => Hash::make('password'),
    ]);

    visit('/')
        ->type('username', $user->username)
        ->type('password', 'password')
        ->press('Log in')
        ->assertPathIs('/');
});

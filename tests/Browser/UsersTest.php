<?php

use App\Models\Section;
use App\Models\User;
use function Pest\Laravel\actingAs;

beforeEach(function () {
    $this->sysop = User::factory()->create();
    $home = Section::factory()->for($this->sysop, 'moderator')->create([
        'parent_id' => null,
    ]);

    $this->moderator = User::factory()->create();
    Section::factory()
        ->for($this->moderator, 'moderator')
        ->for($home, 'parent')
        ->create();
});

test('user list contains users', function () {
    $user = User::factory()->create();

    actingAs($user);

    visit('/users/')
        ->assertSee($this->sysop->name)
        ->assertSee($this->moderator->name)
        ->assertSee($user->name);
});

test('user list can be filtered', function () {
    $name = 'Sir Professor Doctor Test';
    $user = User::factory()->create(['name' => $name]);

    actingAs($user);

    visit('/users/')
        ->assertSee($this->sysop->name)
        ->assertSee($this->moderator->name)
        ->assertSee($user->name)
        ->type('@user-filter', $name)
        ->assertSee($user->name)
        ->assertDontSee($this->sysop->name)
        ->assertDontSee($this->moderator->name)
        ->type('@user-filter', 'this-is-unlikely-to-be-randomly-matched')
        ->assertSee('No users found for ')
        ->clear('@user-filter')
        ->assertDontSee('No users found for ')
        ->assertSee($this->sysop->name)
        ->assertSee($this->moderator->name)
        ->assertSee($user->name);
});

<?php

use App\Models\Section;
use App\Models\Theme;
use App\Models\User;
use function Pest\Laravel\actingAs;

beforeEach(function () {
    $this->defaultTheme = Theme::firstOrFail();
    $this->alternativeTheme = Theme::factory()->create();
    $this->user = User::factory()->create();
    $this->home = Section::factory()->create([
        'parent_id' => null,
        'user_id' => $this->user->id,
    ]);
});

test('new user has default theme', function () {
    expect($this->user->theme->id)->toBe($this->defaultTheme->id);
});

test('user can see which theme they use', function () {
    actingAs($this->user);

    visit('/users/'.$this->user->username)
        ->assertSelected('@theme_select', $this->defaultTheme->id);
});

test('user can change theme', function () {
    actingAs($this->user);

    visit('/users/'.$this->user->username)
        ->assertPresent('option[value="'.$this->alternativeTheme->id.'"]')
        ->select('@theme_select', $this->alternativeTheme->id)
        ->press('Save Changes')
        ->assertPathIs('/users/'.$this->user->username)
        ->assertSelected('@theme_select', $this->alternativeTheme->id);
});

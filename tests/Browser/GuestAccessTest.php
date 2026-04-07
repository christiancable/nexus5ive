<?php

use App\Models\Post;
use App\Models\Section;
use App\Models\Topic;
use App\Models\User;
use function Pest\Laravel\actingAs;

beforeEach(function () {
    $this->guest = User::factory()->create(['is_guest' => true]);
    $this->normalUser = User::factory()->create();

    $this->home = Section::factory()
        ->for($this->normalUser, 'moderator')
        ->create(['parent_id' => null]);

    $this->section = Section::factory()
        ->for($this->normalUser, 'moderator')
        ->for($this->home, 'parent')
        ->create();

    $this->topic = Topic::factory()
        ->for($this->section)
        ->create(['readonly' => false]);

    Post::factory()
        ->for($this->topic)
        ->for($this->normalUser, 'author')
        ->create();
});

test('guest cannot see subscribe button on topic', function () {
    actingAs($this->guest);

    visit('/topic/'.$this->topic->id)
        ->assertDontSee('Unsubscribe from this topic')
        ->assertDontSee('Subscribe to this topic');
});

test('guest sees read only profile not edit form', function () {
    actingAs($this->guest);

    visit('/user/'.$this->guest->username)
        ->assertDontSee('Save Changes');
});

test('guest cannot see comment form on profile', function () {
    actingAs($this->guest);

    visit('/user/'.$this->normalUser->username)
        ->assertDontSee('Add Comment');
});

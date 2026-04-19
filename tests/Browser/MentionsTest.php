<?php

use App\Models\Post;
use App\Models\Section;
use App\Models\Topic;
use App\Models\User;
use function Pest\Laravel\actingAs;

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->anotherUser = User::factory()->create();
    $this->home = Section::factory()->create([
        'parent_id' => null,
        'user_id' => $this->user->id,
    ]);
    $this->topic = Topic::factory()->create([
        'section_id' => $this->home->id,
    ]);
});

test('user with no mentions does not see option to clear mentions', function () {
    actingAs($this->user);

    visit('/')
        ->assertMissing('@mentions-count');
});

test('user with mentions can see they have mentions', function () {
    $post = Post::factory()->create([
        'topic_id' => $this->topic->id,
        'user_id' => $this->anotherUser->id,
    ]);
    $this->user->addMention($post);

    actingAs($this->user);

    visit('/')
        ->assertPresent('@mentions-count');
});

test('user with mentions can clear mentions', function () {
    $post = Post::factory()->create([
        'topic_id' => $this->topic->id,
        'user_id' => $this->anotherUser->id,
    ]);
    $this->user->addMention($post);

    actingAs($this->user);

    visit('/')
        ->click('@mentions-menu-toggle')
        ->press('@mentions-clear')
        ->assertMissing('@mentions-count');
});

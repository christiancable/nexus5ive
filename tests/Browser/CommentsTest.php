<?php

use App\Models\Comment;
use App\Models\Section;
use App\Models\User;
use function Pest\Laravel\actingAs;

beforeEach(function () {
    $sysop = User::factory()->create();
    Section::factory()->create([
        'parent_id' => null,
        'user_id' => $sysop->id,
    ]);

    $this->user = User::factory()->create();
    $this->user2 = User::factory()->create();
});

test('user can clear single comment', function () {
    $comment1 = Comment::factory()->create([
        'user_id' => $this->user->id,
        'author_id' => $this->user2->id,
    ]);

    $comment2 = Comment::factory()->create([
        'user_id' => $this->user->id,
        'author_id' => $this->user2->id,
    ]);

    actingAs($this->user);

    visit('/users/'.$this->user->username)
        ->assertSee($comment1->text)
        ->assertSee($comment2->text)
        ->click('.user-comments tr:first-child button.btn-danger');

    visit('/users/'.$this->user->username)
        ->assertDontSee($comment2->text)
        ->assertSee($comment1->text);
});

test('user can clear all comments', function () {
    $comment1 = Comment::factory()->create([
        'user_id' => $this->user->id,
        'author_id' => $this->user2->id,
    ]);

    $comment2 = Comment::factory()->create([
        'user_id' => $this->user->id,
        'author_id' => $this->user2->id,
    ]);

    actingAs($this->user);

    visit('/users/'.$this->user->username)
        ->assertSee($comment1->text)
        ->assertSee($comment2->text)
        ->press('@btn-clear-all-comments')
        ->assertDontSee($comment1->text)
        ->assertDontSee($comment2->text);
});

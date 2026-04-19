<?php

use App\Models\Section;
use App\Models\User;
use function Pest\Laravel\actingAs;

beforeEach(function () {
    $sysop = User::factory()->create();
    Section::factory()->create([
        'parent_id' => null,
        'user_id' => $sysop->id,
    ]);

    $this->user1 = User::factory()->create();
    $this->user2 = User::factory()->create();
});

test('user can message another user', function () {
    $messageText = 'Hello, how are you?';

    actingAs($this->user1);

    visit('/chat/')
        ->select('#usersDropdown', $this->user2->id)
        ->type('@chat-input', $messageText)
        ->press('@chat-send-button')
        ->assertSeeIn('@chat-messages', $messageText)
        ->assertSeeIn('@chat-list', $this->user2->username);
});

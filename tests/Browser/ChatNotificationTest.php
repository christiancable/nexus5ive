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
    $this->user3 = User::factory()->create();
    $this->user4 = User::factory()->create();
});

test('user can see chat notifications', function () {
    actingAs($this->user1);

    $page = visit('/')
        ->click('#profiledropdown')
        ->assertMissing('@chat-notification-count')
        ->assertMissing('@chat-notifications');

    App\Helpers\ChatHelper::sendMessage($this->user2->id, $this->user1->id, 'Hello from user 2');
    App\Helpers\ChatHelper::sendMessage($this->user3->id, $this->user1->id, 'Hello from user 3');
    App\Helpers\ChatHelper::sendMessage($this->user4->id, $this->user1->id, 'Hello from user 4');

    $page->navigate('/')
        ->click('#profiledropdown')
        ->assertPresent('@chat-notification-count')
        ->assertSeeIn('@chat-notification-count', '3')
        ->assertPresent('@chat-notifications')
        ->assertSeeIn('@chat-notifications', $this->user2->username)
        ->assertSeeIn('@chat-notifications', $this->user3->username)
        ->assertSeeIn('@chat-notifications', $this->user4->username);

    $page->navigate('/chat/'.$this->user2->username)
        ->click('#profiledropdown')
        ->assertPresent('@chat-notification-count')
        ->assertSeeIn('@chat-notification-count', '2')
        ->assertPresent('@chat-notifications')
        ->assertDontSeeIn('@chat-notifications', $this->user2->username)
        ->assertSeeIn('@chat-notifications', $this->user3->username)
        ->assertSeeIn('@chat-notifications', $this->user4->username);
});

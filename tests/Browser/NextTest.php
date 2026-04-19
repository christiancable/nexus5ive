<?php

use App\Models\Post;
use App\Models\Section;
use App\Models\Topic;
use App\Models\User;
use function Pest\Laravel\actingAs;

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->home = Section::factory()->create([
        'parent_id' => null,
        'user_id' => $this->user->id,
    ]);
    $this->section1 = Section::factory()->create([
        'parent_id' => $this->home->id,
        'user_id' => $this->user->id,
    ]);
    $this->section2 = Section::factory()->create([
        'parent_id' => $this->home->id,
        'user_id' => $this->user->id,
    ]);
    $this->topic1 = Topic::factory()->create([
        'section_id' => $this->home->id,
    ]);
    $this->topic2 = Topic::factory()->create([
        'section_id' => $this->home->id,
    ]);
});

$noTopicsMsg = 'No updated topics found. Why not start a new conversation or read more sections?';
$newTopicsMsg = 'People have been talking! New posts found in ';

test('user can jump to next updated topic', function () use ($newTopicsMsg) {
    App\Helpers\ViewHelper::subscribeToTopic($this->user, $this->topic1);

    Post::factory()->create([
        'topic_id' => $this->topic1->id,
        'user_id' => $this->user->id,
    ]);

    actingAs($this->user);

    visit('/')
        ->press('@toolbar-next')
        ->assertPathIs('/section/'.$this->topic1->section->id)
        ->assertSee($newTopicsMsg.$this->topic1->title);
});

test('user does not jump to topic when no topic has been updated', function () use ($noTopicsMsg) {
    actingAs($this->user);

    visit('/')
        ->press('@toolbar-next')
        ->assertPathIs('/section/'.$this->home->id)
        ->assertSee($noTopicsMsg);
});

test('user does not jump to next unsubscribed topic', function () use ($noTopicsMsg) {
    App\Helpers\ViewHelper::unsubscribeFromTopic($this->user, $this->topic1);

    Post::factory()->create([
        'topic_id' => $this->topic1->id,
        'user_id' => $this->user->id,
    ]);

    actingAs($this->user);

    visit('/')
        ->press('@toolbar-next')
        ->assertPathIs('/section/'.$this->home->id)
        ->assertSee($noTopicsMsg);
});

test('leap does not crash when subscribed topic has only undated posts', function () use ($noTopicsMsg) {
    App\Helpers\ViewHelper::subscribeToTopic($this->user, $this->topic1);

    Post::factory()->create([
        'topic_id' => $this->topic1->id,
        'user_id' => $this->user->id,
        'time' => null,
    ]);

    actingAs($this->user);

    visit('/')
        ->press('@toolbar-next')
        ->assertSee($noTopicsMsg);
});

test('leap works correctly when topic has mix of dated and undated posts', function () use ($newTopicsMsg) {
    App\Helpers\ViewHelper::subscribeToTopic($this->user, $this->topic1);

    Post::factory()->create([
        'topic_id' => $this->topic1->id,
        'user_id' => $this->user->id,
        'time' => null,
    ]);

    Post::factory()->create([
        'topic_id' => $this->topic1->id,
        'user_id' => $this->user->id,
    ]);

    actingAs($this->user);

    visit('/')
        ->press('@toolbar-next')
        ->assertPathIs('/section/'.$this->topic1->section->id)
        ->assertSee($newTopicsMsg.$this->topic1->title);
});

test('user can mark all subscribed topics as read', function () use ($noTopicsMsg) {
    App\Helpers\ViewHelper::subscribeToTopic($this->user, $this->topic1);

    Post::factory()->create([
        'topic_id' => $this->topic1->id,
        'user_id' => $this->user->id,
    ]);

    actingAs($this->user);

    visit('/')
        ->press('@toolbar-next')
        ->click('mark all subscribed topics as read')
        ->press('@toolbar-next')
        ->assertSee($noTopicsMsg);
});

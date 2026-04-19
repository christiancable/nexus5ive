<?php

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
    $this->topic = Topic::factory()->create([
        'section_id' => $this->home->id,
    ]);
});

test('user can unsubscribe from topic', function () {
    actingAs($this->user);

    visit('/topic/'.$this->topic->id)
        ->press('Unsubscribe from this topic')
        ->assertSee('Subscribe to this topic');
});

test('user can resubscribe to topic', function () {
    App\Helpers\ViewHelper::unsubscribeFromTopic($this->user, $this->topic);

    actingAs($this->user);

    visit('/topic/'.$this->topic->id)
        ->press('Subscribe to this topic')
        ->assertSee('Unsubscribe from this topic');
});

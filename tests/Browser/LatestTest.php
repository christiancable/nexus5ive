<?php

use App\Helpers\NxCodeHelper;
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
    $this->topic = Topic::factory()->create([
        'section_id' => $this->home->id,
    ]);
    $this->emptyTopic = Topic::factory()->create([
        'section_id' => $this->home->id,
    ]);
    $this->post = Post::factory()->create([
        'topic_id' => $this->topic->id,
        'user_id' => $this->user->id,
    ]);
    $this->postPreview = substr(strip_tags(NxCodeHelper::nxDecode($this->post->text)), 0, 140);
});

test('user sees post preview for topic with posts', function () {
    actingAs($this->user);

    visit('/section/latest')
        ->assertSee($this->postPreview);
});

test('user does not see post preview for unsubscribed topic with posts', function () {
    actingAs($this->user);

    visit('/topic/'.$this->topic->id)
        ->press('Unsubscribe from this topic')
        ->assertSee('Subscribe to this topic')
        ->navigate('/section/latest/')
        ->assertDontSee($this->postPreview);
});

test('user cannot see empty topic listed in latest', function () {
    actingAs($this->user);

    visit('/section/latest/')
        ->assertDontSee($this->emptyTopic->title);
});

test('user can see posted-to topic listed in latest', function () {
    Post::factory()->create([
        'topic_id' => $this->emptyTopic->id,
        'user_id' => $this->user->id,
    ]);

    actingAs($this->user);

    visit('/section/latest/')
        ->assertSee($this->emptyTopic->title);
});

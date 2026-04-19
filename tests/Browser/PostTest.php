<?php

use App\Http\Controllers\Nexus\TopicController;
use App\Models\Section;
use App\Models\Topic;
use App\Models\User;
use function Pest\Laravel\actingAs;

beforeEach(function () {
    $this->sysop = User::factory()->create(['administrator' => true]);
    $this->moderator = User::factory()->create();
    $this->normalUser = User::factory()->create();

    $this->home = Section::factory()->for($this->sysop, 'moderator')->create([
        'parent_id' => null,
    ]);
    $this->subSection = Section::factory()
        ->for($this->moderator, 'moderator')
        ->for($this->home, 'parent')
        ->create();

    $this->topic = Topic::factory()
        ->for($this->subSection, 'section')
        ->create();

    $this->closedTopic = Topic::factory()
        ->for($this->subSection, 'section')
        ->create(['readonly' => true]);
});

test('user can post in topic', function () {
    $title = 'Hello Everyone!';
    $text = 'this is a test go back to sleep';

    actingAs($this->normalUser);

    visit(action([TopicController::class, 'show'], ['topic' => $this->topic]))
        ->type('title', $title)
        ->type('text', $text)
        ->press('Add Comment')
        ->assertSeeIn('.card-title', $title)
        ->assertSee($text);
});

test('user cannot post in read only topic', function () {
    actingAs($this->normalUser);

    visit(action([TopicController::class, 'show'], ['topic' => $this->closedTopic]))
        ->assertDontSee('Add Comment')
        ->assertSee(strip_tags(__('nexus.topic.closed.normal')));
});

test('owner can post in read only topic with warning', function () {
    $title = 'Hello Everyone!';
    $text = 'this is a test go back to sleep';

    actingAs($this->moderator);

    visit(action([TopicController::class, 'show'], ['topic' => $this->closedTopic]))
        ->assertSee(strip_tags(__('nexus.topic.closed.moderator')))
        ->type('title', $title)
        ->type('text', $text)
        ->press('Add Comment')
        ->assertSeeIn('.card-title', $title)
        ->assertSee($text);
});

test('admin can post in read only topic with warning', function () {
    $title = 'Hello Everyone!';
    $text = 'this is a test go back to sleep';

    actingAs($this->sysop);

    visit(action([TopicController::class, 'show'], ['topic' => $this->closedTopic]))
        ->assertSee(strip_tags(__('nexus.topic.closed.moderator')))
        ->type('title', $title)
        ->type('text', $text)
        ->press('Add Comment')
        ->assertSeeIn('.card-title', $title)
        ->assertSee($text);
});

test('user cannot post empty post in topic', function () {
    actingAs($this->normalUser);

    visit(action([TopicController::class, 'show'], ['topic' => $this->topic]))
        ->type('title', 'Hello Everyone!')
        ->press('Add Comment')
        ->assertSeeIn('.alert-danger', strip_tags(__('nexus.validation.post.empty')));
});

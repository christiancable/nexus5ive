<?php

use App\Http\Controllers\Nexus\SectionController;
use App\Models\Post;
use App\Models\Section;
use App\Models\Topic;
use App\Models\User;
use function Pest\Laravel\actingAs;

beforeEach(function () {
    $this->moderator = User::factory()->create();
    $this->normalUser = User::factory()->create();

    $this->home = Section::factory()
        ->for($this->moderator, 'moderator')
        ->create(['parent_id' => null]);

    $this->section = Section::factory()
        ->for($this->moderator, 'moderator')
        ->for($this->home, 'parent')
        ->create(['allow_user_topics' => false]);
});

test('section edit form has allow user topics checkbox', function () {
    actingAs($this->moderator);

    $page = visit(action([SectionController::class, 'show'], ['section' => $this->section]));

    $page->script("document.querySelector('#section-edit{$this->section->id}').classList.add('show', 'active');");

    $page->assertPresent('#allow_user_topics_'.$this->section->id)
        ->assertSee('Allow all users to create topics');
});

test('moderator sees all topic options', function () {
    actingAs($this->moderator);

    visit(action([SectionController::class, 'show'], ['section' => $this->section]))
        ->click('[data-bs-target="#addTopicForm"]')
        ->assertVisible('input#secret[type="checkbox"]')
        ->assertVisible('input#readonly[type="checkbox"]')
        ->assertVisible('select[name="weight"]');
});

test('normal user does not see add topic when disabled', function () {
    actingAs($this->normalUser);

    visit(action([SectionController::class, 'show'], ['section' => $this->section]))
        ->assertDontSee('Add New Topic');
});

test('normal user sees add topic when enabled', function () {
    $this->section->update(['allow_user_topics' => true]);

    actingAs($this->normalUser);

    visit(action([SectionController::class, 'show'], ['section' => $this->section]))
        ->assertSee('Add New Topic');
});

test('normal user does not see moderator only options', function () {
    $this->section->update(['allow_user_topics' => true]);

    actingAs($this->normalUser);

    visit(action([SectionController::class, 'show'], ['section' => $this->section]))
        ->click('[data-bs-target="#addTopicForm"]')
        ->assertMissing('input[name="secret"][type="checkbox"]')
        ->assertMissing('input[name="readonly"][type="checkbox"]')
        ->assertMissing('select[name="weight"]');
});

test('normal user can create topic', function () {
    $this->section->update(['allow_user_topics' => true]);

    actingAs($this->normalUser);

    visit(action([SectionController::class, 'show'], ['section' => $this->section]))
        ->click('[data-bs-target="#addTopicForm"]')
        ->type('title', 'My New Topic')
        ->type('intro', 'This is my topic introduction')
        ->press('Add Topic')
        ->assertSee('My New Topic');

    $topic = Topic::where('title', 'My New Topic')->first();
    expect($topic)->not->toBeNull();
    expect($topic->secret)->toBe(0);
    expect($topic->readonly)->toBe(0);
    expect($topic->weight)->toBe(0);

    $post = Post::where('topic_id', $topic->id)->first();
    expect($post)->not->toBeNull();
    expect($post->text)->toBe('This is my topic introduction');
    expect($post->user_id)->toBe($this->normalUser->id);
});

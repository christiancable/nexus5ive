<?php

use App\Http\Controllers\Nexus\TopicController;
use App\Models\Post;
use App\Models\Section;
use App\Models\Topic;
use App\Models\User;

use function Pest\Laravel\actingAs;

// XSS detection strategy: if the payload is rendered as raw HTML, an <img> element
// appears in the DOM. assertMissing('img#xss-marker') passes when the value is
// properly escaped and fails when it is not.
const XSS_IMG_PAYLOAD = '<img src=x id="xss-marker">';

beforeEach(function () {
    $this->moderator = User::factory()->create();

    $this->home = Section::factory()
        ->for($this->moderator, 'moderator')
        ->create(['parent_id' => null]);

    $this->section = Section::factory()
        ->for($this->moderator, 'moderator')
        ->for($this->home, 'parent')
        ->create();

    $this->topic = Topic::factory()
        ->for($this->section, 'section')
        ->create();
});

// --- Issue 1: editedByInfo XSS (app/View/Components/Post.php:69) ---
//
// The "Edited by" footer interpolates $post->editor->username into a raw HTML
// string without escaping, then outputs it with {!! $editedByInfo !!}.
// Username validation allows any string up to 255 chars — no HTML metacharacters
// are blocked.
//
// This test FAILS until Post.php is fixed to wrap the username with e().

test('xss payload in editor username is escaped in the edited-by footer', function () {
    $attacker = User::factory()->create(['username' => XSS_IMG_PAYLOAD]);
    $author = User::factory()->create();

    // Simulate the attacker having edited a post by setting update_user_id directly.
    // This bypasses the edit form intentionally — the vulnerability is in rendering,
    // not in how update_user_id gets set.
    Post::factory()
        ->for($this->topic, 'topic')
        ->for($author, 'author')
        ->create(['update_user_id' => $attacker->id]);

    actingAs($author);

    visit(action([TopicController::class, 'show'], ['topic' => $this->topic]))
        ->assertMissing('img#xss-marker'); // fails if XSS is not escaped
});

// --- Issue 2: popname XSS (resources/views/nexus/users/_panel.blade.php:11) ---
//
// _panel.blade.php renders $user->popname with {!! !!} after raw PHP string
// interpolation, with no HTML encoding. However, this partial is currently
// orphaned — no active route includes it. All active views (user-card.blade.php,
// show.blade.php, profile-menu.blade.php) use {{ }} and are safe.
//
// This test confirms the active profile page does NOT execute the payload.
// It would need to be complemented by a route-level test if _panel.blade.php
// is ever re-introduced.

test('xss payload in popname is escaped on the active user profile page', function () {
    $target = User::factory()->create(['popname' => XSS_IMG_PAYLOAD]);
    $viewer = User::factory()->create();

    actingAs($viewer);

    // show.blade.php passes popname through {{ }} in x-heading — should be safe.
    visit('/users/'.$target->username)
        ->assertMissing('img#xss-marker'); // passes because show.blade.php escapes correctly
});

<?php

use App\Models\Post;
use App\Models\Section;
use App\Models\Topic;
use App\Models\User;
use Illuminate\Support\Facades\Cache;

use function Pest\Laravel\actingAs;

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->home = Section::factory()->create([
        'parent_id' => null,
        'user_id' => $this->user->id,
    ]);
    $this->subSection = Section::factory()->create([
        'parent_id' => $this->home,
        'user_id' => $this->user->id,
    ]);
    $this->topicInSubSection = Topic::factory()->create([
        'section_id' => $this->subSection->id,
    ]);
    $this->anotherTopicInSubSection = Topic::factory()->create([
        'section_id' => $this->subSection->id,
    ]);

    // Flush the entire in-memory cache after model creation.
    // Section::created fires TreeHelper::rebuild(), which reads and caches mostRecentPost{id}
    // for every section. If a previous test left a stale entry (DatabaseTruncation only clears
    // DB tables, not the array cache store), the wrong "Latest Post in" text can appear.
    Cache::flush();
});

test('section info shows which topic has the most recent post', function () {
    Post::factory()->create([
        'topic_id' => $this->topicInSubSection->id,
        'user_id' => $this->user->id,
    ]);

    Section::forgetMostRecentPostAttribute($this->home->id);
    Section::forgetMostRecentPostAttribute($this->subSection->id);

    actingAs($this->user);

    visit('/section/'.$this->home->id)
        ->assertSee('Latest Post in '.$this->topicInSubSection->title);
});

test('section with no topics shows no topic as having the most recent post', function () {
    actingAs($this->user);

    visit('/section/'.$this->home->id)
        ->assertDontSee('Latest Post in ');
});

test('section info updates latest post found in when new posts are added', function () {
    Post::factory()->create([
        'topic_id' => $this->topicInSubSection->id,
        'user_id' => $this->user->id,
    ]);

    Section::forgetMostRecentPostAttribute($this->home->id);
    Section::forgetMostRecentPostAttribute($this->subSection->id);

    actingAs($this->user);

    $page = visit('/section/'.$this->home->id)
        ->assertSee('Latest Post in '.$this->topicInSubSection->title);

    Post::factory()->create([
        'topic_id' => $this->anotherTopicInSubSection->id,
        'user_id' => $this->user->id,
    ]);

    Section::forgetMostRecentPostAttribute($this->home->id);
    Section::forgetMostRecentPostAttribute($this->subSection->id);

    $page->navigate('/section/'.$this->home->id)
        ->assertSee('Latest Post in '.$this->anotherTopicInSubSection->title);
});

<?php

namespace Tests\Feature;

use App\Models\Post;
use App\Models\Section;
use App\Models\Topic;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class SearchControllerTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->forTheme()->create();

        // Home section required for breadcrumbs
        $owner = User::factory()->forTheme()->create();
        Section::factory()->for($owner, 'moderator')->create(['parent_id' => null]);
    }

    // index

    #[Test]
    public function authenticated_user_can_view_search_page(): void
    {
        $this->actingAs($this->user)
            ->get('/search')
            ->assertOk();
    }

    #[Test]
    public function unauthenticated_user_is_redirected_from_search(): void
    {
        $this->get('/search')
            ->assertRedirect(route('login'));
    }

    // submitSearch

    #[Test]
    public function submit_search_redirects_to_find(): void
    {
        $response = $this->actingAs($this->user)
            ->post('/search', ['text' => 'hello']);

        $response->assertRedirect('/search/hello');
    }

    #[Test]
    public function submit_search_requires_text(): void
    {
        $response = $this->actingAs($this->user)
            ->post('/search', ['text' => '']);

        $response->assertSessionHasErrorsIn('submitSearch', 'text');
    }

    // find

    #[Test]
    public function find_returns_results_for_matching_word(): void
    {
        $owner = User::factory()->forTheme()->create();
        $section = Section::factory()->for($owner, 'moderator')->for(Section::first(), 'parent')->create();
        $topic = Topic::factory()->for($section)->create();
        Post::factory()->for($topic)->for($owner, 'author')->create(['text' => 'uniquewordxyz in this post']);

        $response = $this->actingAs($this->user)
            ->get('/search/uniquewordxyz');

        $response->assertOk();
        $response->assertSee('uniquewordxyz');
    }

    #[Test]
    public function find_returns_view_with_no_results_when_nothing_matches(): void
    {
        $this->actingAs($this->user)
            ->get('/search/zzznomatchzzz')
            ->assertOk();
    }

    #[Test]
    public function find_with_multiple_words_matches_posts_containing_all_terms(): void
    {
        $owner = User::factory()->forTheme()->create();
        $section = Section::factory()->for($owner, 'moderator')->for(Section::first(), 'parent')->create();
        $topic = Topic::factory()->for($section)->create();
        Post::factory()->for($topic)->for($owner, 'author')->create(['text' => 'alpha beta gamma in here']);

        $response = $this->actingAs($this->user)
            ->get('/search/alpha%20beta');

        $response->assertOk();
        $response->assertSee('alpha');
    }

    #[Test]
    public function find_with_stop_words_only_returns_no_results(): void
    {
        // "the" is a stop word — searching for it should not blow up
        $this->actingAs($this->user)
            ->get('/search/the')
            ->assertOk();
    }

    #[Test]
    public function find_with_quoted_phrase_performs_phrase_search(): void
    {
        $owner = User::factory()->forTheme()->create();
        $section = Section::factory()->for($owner, 'moderator')->for(Section::first(), 'parent')->create();
        $topic = Topic::factory()->for($section)->create();
        Post::factory()->for($topic)->for($owner, 'author')->create(['text' => 'exact phrase match here']);

        $response = $this->actingAs($this->user)
            ->get('/search/'.rawurlencode('"exact phrase"'));

        $response->assertOk();
        $response->assertSee('exact phrase');
    }
}

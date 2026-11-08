<?php

namespace App\Http\Controllers\Nexus;

use App\Helpers\ActivityHelper;
use App\Helpers\BreadcrumbHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\Nexus\SearchRequest;
use App\Models\Post;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SearchController extends Controller
{
    private static array $stopWords = [
        'the', 'and', 'an', 'of',
    ];

    /**
     * Display a search page
     */
    public function index(Request $request): View
    {
        $text = 'Search';
        $results = null;
        $breadcrumbs = BreadcrumbHelper::breadcumbForUtility('Search');

        ActivityHelper::updateActivity(
            $request->user()->id,
            'Searching',
            route('search.index')
        );

        return view(
            'nexus.search.results',
            compact('results', 'breadcrumbs', 'text')
        );
    }

    /**
     * submit the search request
     */
    public function submitSearch(SearchRequest $request): RedirectResponse
    {
        $input = $request->all();
        $searchText = $input['text'];

        return redirect()->route('search.find', ['text' => $searchText]);
    }

    /**
     * perform a search against all the posts and return results
     */
    public function find(Request $request, string $text): View
    {
        $results = $this->buildSearchQuery($text);

        ActivityHelper::updateActivity(
            $request->user()->id,
            'Searching',
            route('search.index')
        );

        $breadcrumbs = BreadcrumbHelper::breadcumbForUtility('Search');
        $displaySearchResults = true;

        return view(
            'nexus.search.results',
            compact('results', 'breadcrumbs', 'text', 'displaySearchResults')
        );
    }

    private function buildSearchQuery(string $text): ?Builder
    {
        // Wrap in quotes/apostrophes to search for an exact phrase
        if (preg_match('/^[\'"](.+)[\'"]$/', $text, $matches)) {
            return Post::where('text', 'like', '%'.trim($matches[1]).'%')->orderBy('time', 'desc');
        }

        $words = array_filter(
            explode(' ', $text),
            fn (string $word) => strlen(trim($word)) > 0 && ! in_array(strtolower($word), self::$stopWords)
        );

        if (empty($words)) {
            return null;
        }

        $query = Post::query();
        foreach ($words as $word) {
            $query->where('text', 'like', '%'.trim($word).'%');
        }

        return $query->orderBy('time', 'desc');
    }
}

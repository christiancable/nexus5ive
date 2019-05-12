<?php

namespace App\Http\Controllers\Nexus;

use App\Post;
use App\Http\Requests;
use Illuminate\View\View;
use Illuminate\Http\Request;
use App\Helpers\ActivityHelper;
use App\Helpers\BreadcrumbHelper;
use App\Http\Requests\SearchRequest;
use App\Http\Controllers\Controller;

class SearchController extends Controller
{
    private static $stopWords = [
        'the','and','an','of',
    ];

    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('verified');
    }

    /**
     * Display a search page
     *
     * @param Request $request
     * @return View
     */
    public function index(Request $request)
    {
        $text = 'Search';
        $results = null;
        $breadcrumbs = BreadcrumbHelper::breadcumbForUtility('Search');

        ActivityHelper::updateActivity(
            $request->user()->id,
            "Searching",
            action('Nexus\SearchController@index')
        );

        return view(
            'search.results',
            compact('results', 'breadcrumbs', 'text')
        );
    }

    /**
     * submit the search request
     */
    public function submitSearch(SearchRequest $request)
    {
        $input = $request->all();
        $searchText = $input['text'];
        
        return redirect(action('Nexus\SearchController@find', ['text' => $searchText]));
    }

    /**
     * perform a search against all the posts and
     * return some results
     *
     * @param Request $request
     * @param String $text
     * @return View
     *
     * @todo - ignore word order
     * @todo - remove stop words
     * @todo - deal with exact phrases
     */
    public function find(Request $request, $text)
    {

        $phraseSearch = false;
        $displaySearchResults = true;

        // if text is ^"(.*)"$ or ^'(.*)'$ then we are searching for a phrase
        $pattern = <<< 'pattern'
/^['|"](.*)['|"]$/
pattern;
        
        $matches = false;
        preg_match($pattern, $text, $matches);

        if (!$matches) {
            // set initial results as nothing
            $results = false;

            // look for all the words
            $rawSearchTerms = explode(' ', $text);
            $searchTerms = [];

            // remove stop words here
            foreach ($rawSearchTerms as $word) {
                if (!in_array(strtolower($word), self::$stopWords)) {
                    $searchTerms[] = $word;
                }
            }
        
            // dd($searchTerms);

            foreach ($searchTerms as $word) {
                // remove unwanted characters from the start and end
                // @todo this does not remove multiple unwanted commas etc
                $word = trim($word);

                if (strlen($word) !== 0) {
                    if ($results) {
                        $results = $results->where('text', 'like', "%$word%");
                    } else {
                        // first where
                        $results = Post::where('text', 'like', "%$word%");
                    }
                }
            }
        } else {
            $phrase = trim($matches[1]);
            $results = Post::where('text', 'like', "%$phrase%");
        }

        if ($results) {
            $results->orderBy('time', 'desc');
        }

        ActivityHelper::updateActivity(
            $request->user()->id,
            "Searching",
            action('Nexus\SearchController@index')
        );

        $breadcrumbs = BreadcrumbHelper::breadcumbForUtility('Search');

        return view(
            'search.results',
            compact('results', 'breadcrumbs', 'text', 'displaySearchResults')
        );
    }
}

<?php

namespace Nexus\Http\Controllers\Nexus;

use Illuminate\Http\Request;

use Nexus\Http\Requests;
use Nexus\Http\Controllers\Controller;

class SearchController extends Controller
{
    private static $stopWords = array(
        'the','and','an','of',
    );

    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a search page
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $text = 'Search';
        $results = null;
        $breadcrumbs = \Nexus\Helpers\BreadcrumbHelper::breadcumbForUtility('Search');

        return view(
            'search.results',
            compact('results', 'breadcrumbs', 'text')
        );
    }

    /**
     *
     * @todo validation of search via request
     *
     */
    public function submitSearch(Requests\Search\SearchRequest $request)
    {
        $input = $request->all();
        $searchText = $input['text'];
        
        $redirect = action('Nexus\SearchController@find', ['text' => $searchText]);
        return redirect($redirect);
    }


    /**
     * perform a search against all the posts and
     * return some results
     * @todo - ignore word order
     * @todo - remove stop words
     * @todo - deal with exact phrases
     */
    public function find($text)
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
            $searchTerms = array();

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
                        $results = \Nexus\Post::where('text', 'like', "%$word%");
                    }
                }
            }
        } else {
            $phrase = trim($matches[1]);
            $results = \Nexus\Post::where('text', 'like', "%$phrase%");
        }

        if ($results) {
            $results->orderBy('time', 'desc');
        }

        \Nexus\Helpers\ActivityHelper::updateActivity(
            "Searching",
            action('Nexus\SearchController@index'),
            \Auth::user()->id
        );

        $breadcrumbs = \Nexus\Helpers\BreadcrumbHelper::breadcumbForUtility('Search');

        return view(
            'search.results',
            compact('results', 'breadcrumbs', 'text', 'displaySearchResults')
        );
    }
}

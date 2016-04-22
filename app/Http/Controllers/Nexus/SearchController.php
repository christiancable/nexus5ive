<?php

namespace Nexus\Http\Controllers\Nexus;

use Illuminate\Http\Request;

use Nexus\Http\Requests;
use Nexus\Http\Controllers\Controller;

class SearchController extends Controller
{
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
        //
    }

    /**
     * perform a search against all the posts and
     * return some results
     *
     * @todo - remove stop words
     * @todo - deal with exact phrases
     */
    public function find($text)
    {
        $results = \Nexus\Post::where('text', 'like', "%$text%")->orderBy('time','desc');

        \Nexus\Helpers\ActivityHelper::updateActivity(
            "Searching",
            action('Nexus\SearchController@index'),
            \Auth::user()->id
        );

        $breadcrumbs = \Nexus\Helpers\BreadcrumbHelper::breadcumbForUtility('Search');
        // view with pagination

        return view(
            'search.results',
            compact('results', 'breadcrumbs')
        );
    }
}

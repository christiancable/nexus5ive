<?php

namespace App\Http\Controllers\Nexus;

use App\Section;
use App\Http\Requests;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class RestoreController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $trashedSections = \App\Section::onlyTrashed()
            ->where('user_id', \Auth::user()->id)
            ->with('trashedTopics')
            ->get();

        // add trashed sections which are children of moderated sections which are not moderated by the user
        // @todo: can this be a query?
        foreach (\Auth::user()->sections as $moderatedSections) {
            $unmoderatedSections = $moderatedSections
                ->sections()
                ->onlyTrashed()
                ->with('trashedTopics')
                ->where('user_id', '!=', \Auth::user()->id)
                ->get();
            foreach ($unmoderatedSections as $unmoderatedSection) {
                $trashedSections->push($unmoderatedSection);
            }
        }

        $trashedSections = $trashedSections->sortByDesc('deleted_at');
        $trashedTopics = \Auth::user()->trashedTopics;
        $breadcrumbs = \App\Helpers\BreadcrumbHelper::breadcumbForUtility('Your Archive');
        $destinationSections = \Auth::user()->sections()->get();

        return view('restore.index', compact('trashedSections', 'trashedTopics', 'breadcrumbs', 'destinationSections'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    /**
     * Restore a trashed section
     *
     * @param Requests\Section\RestoreRequest $request
     * @param int $id - the trashed section
     * @return void
     */
    public function section(Requests\Section\RestoreRequest $request, $id)
    {
        
        $trashedSection = \App\Section::onlyTrashed()->findOrFail($id);
        $destinationSection = \App\Section::findOrFail($request->destination);

        $this->authorize('restore', [Section::class, $trashedSection, $destinationSection]);
        \App\Helpers\RestoreHelper::restoreSectionToSection($trashedSection, $destinationSection);
     
        $redirect = action('Nexus\SectionController@show', ['id' => $trashedSection->id]);
        return redirect($redirect);
    }
    
    public function topic(Requests\Topic\RestoreRequest $request, $id)
    {
        $trashedTopic = \App\Topic::onlyTrashed()->findOrFail($id);
        $destinationSection = \App\Section::findOrFail($request->destination);
        
        \App\Helpers\RestoreHelper::restoreTopicToSection($trashedTopic, $destinationSection);
        
        $redirect = action('Nexus\SectionController@show', ['id' => $destinationSection->id]);
        return redirect($redirect);
    }
}

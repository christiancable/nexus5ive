<?php

namespace App\Http\Controllers\Nexus;

use App\Topic;
use App\Section;
use App\Http\Requests;
use Illuminate\View\View;
use Illuminate\Http\Request;
use App\Helpers\RestoreHelper;
use App\Helpers\BreadcrumbHelper;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;

class RestoreController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('verified');
    }
    
    /**
     * Display a listing of the resource.
     *
     * @return View
     */
    public function index(Request $request)
    {
        $trashedSections = Section::onlyTrashed()
            ->where('user_id', $request->user()->id)
            ->with('trashedTopics')
            ->get();

        // add trashed sections which are children of moderated sections which are not moderated by the user
        // @todo: can this be a query?
        foreach ($request->user()->sections as $moderatedSections) {
            $unmoderatedSections = $moderatedSections
                ->sections()
                ->onlyTrashed()
                ->with('trashedTopics')
                ->where('user_id', '!=', $request->user()->id)
                ->get();
            foreach ($unmoderatedSections as $unmoderatedSection) {
                $trashedSections->push($unmoderatedSection);
            }
        }

        $trashedSections = $trashedSections->sortByDesc('deleted_at');
        $trashedTopics = $request->user()->trashedTopics;
        $breadcrumbs = BreadcrumbHelper::breadcumbForUtility('Your Archive');
        $destinationSections = $request->user()->sections()->get();

        return view('restore.index', compact('trashedSections', 'trashedTopics', 'breadcrumbs', 'destinationSections'));
    }


    /**
     * Restore a trashed section
     *
     * @param  Request  $request
     * @param int $id - the trashed section
     * @return RedirectResponse
     */
    public function section(Request $request, $id)
    {
        $trashedSection = Section::onlyTrashed()->findOrFail($id);
        $destinationSection = Section::findOrFail($request->destination);

        $this->authorize('restore', [Section::class, $trashedSection, $destinationSection]);
        RestoreHelper::restoreSectionToSection($trashedSection, $destinationSection);
        
        $redirect = action('Nexus\SectionController@show', ['section' => $trashedSection->id]);
        return redirect($redirect);
    }
    
    /**
     * Restore a trashed topic
     *
     * @param  Request  $request
     * @param int $id - the trashed topic
     * @return RedirectResponse
     */
    public function topic(Request $request, $id)
    {
        $trashedTopic = Topic::onlyTrashed()->findOrFail($id);
        $destinationSection = Section::findOrFail($request->destination);
        
        $this->authorize('restore', [Topic::class, $trashedTopic, $destinationSection]);
        RestoreHelper::restoreTopicToSection($trashedTopic, $destinationSection);
        
        $redirect = action('Nexus\SectionController@show', ['section' => $destinationSection->id]);
        return redirect($redirect);
    }
}

<?php

namespace App\Http\Controllers\Nexus;

use App\Helpers\BreadcrumbHelper;
use App\Helpers\RestoreHelper;
use App\Http\Controllers\Controller;
use App\Models\Section;
use App\Models\Topic;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class RestoreController extends Controller
{
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

        return view('nexus.restore.index', compact('trashedSections', 'trashedTopics', 'breadcrumbs', 'destinationSections'));
    }

    /**
     * Restore a trashed section
     *
     * @param  int  $id  - the trashed section
     * @return RedirectResponse
     */
    public function section(Request $request, $id)
    {
        $trashedSection = Section::onlyTrashed()->findOrFail($id);
        $destinationSection = Section::findOrFail($request->destination);

        if ($request->user()->cannot('restore', [$trashedSection, $destinationSection])) {
            abort(403);
        }

        RestoreHelper::restoreSectionToSection($trashedSection, $destinationSection);

        $redirect = action('App\Http\Controllers\Nexus\SectionController@show', ['section' => $trashedSection->id]);

        return redirect($redirect);
    }

    /**
     * Restore a trashed topic
     *
     * @param  int  $id  - the trashed topic
     * @return RedirectResponse
     */
    public function topic(Request $request, $id)
    {
        $trashedTopic = Topic::onlyTrashed()->findOrFail($id);
        $destinationSection = Section::findOrFail($request->destination);

        $this->authorize('restore', [Topic::class, $trashedTopic, $destinationSection]);
        RestoreHelper::restoreTopicToSection($trashedTopic, $destinationSection);

        $redirect = action('App\Http\Controllers\Nexus\SectionController@show', ['section' => $destinationSection->id]);

        return redirect($redirect);
    }
}

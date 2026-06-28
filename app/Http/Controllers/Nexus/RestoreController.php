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
     */
    public function index(Request $request): View
    {
        $trashedSections = Section::onlyTrashed()
            ->where('user_id', $request->user()->id)
            ->with('trashedTopics')
            ->get();

        // add trashed sections which are children of moderated sections which are not moderated by the user
        $moderatedSectionIds = $request->user()->sections->pluck('id');
        $unmoderatedSections = Section::onlyTrashed()
            ->whereIn('parent_id', $moderatedSectionIds)
            ->where('user_id', '!=', $request->user()->id)
            ->with('trashedTopics')
            ->get();
        foreach ($unmoderatedSections as $unmoderatedSection) {
            $trashedSections->push($unmoderatedSection);
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
     */
    public function section(Request $request, int $id): RedirectResponse
    {
        $trashedSection = Section::onlyTrashed()->findOrFail($id);
        $destinationSection = Section::findOrFail($request->destination);

        if ($request->user()->cannot('restore', [$trashedSection, $destinationSection])) {
            abort(403);
        }

        RestoreHelper::restoreSectionToSection($trashedSection, $destinationSection);

        return redirect()->route('section.show', ['section' => $trashedSection->id]);
    }

    /**
     * Restore a trashed topic
     *
     * @param  int  $id  - the trashed topic
     */
    public function topic(Request $request, int $id): RedirectResponse
    {
        $trashedTopic = Topic::onlyTrashed()->findOrFail($id);
        $destinationSection = Section::findOrFail($request->destination);

        if ($request->user()->cannot('restore', [$trashedTopic, $destinationSection])) {
            abort(403);
        }

        RestoreHelper::restoreTopicToSection($trashedTopic, $destinationSection);

        return redirect()->route('section.show', ['section' => $destinationSection->id]);
    }
}

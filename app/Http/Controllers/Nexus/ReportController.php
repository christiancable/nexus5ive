<?php

namespace App\Http\Controllers\Nexus;

use App\Helpers\BreadcrumbHelper;
use App\Helpers\FlashHelper;
use App\Http\Controllers\Controller;
use App\Models\Report;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class ReportController extends Controller
{
    /**
     * index of currently open reports aka the moderation queue
     */
    public function index()
    {
        $breadcrumbs = BreadcrumbHelper::breadcumbForUtility('Moderation Queue');

        $reports = Report::open()
            ->with(['reportable', 'reporter', 'moderator']) // eager load relations
            ->orderBy('created_at', 'desc')
            ->paginate(5);

        return view('nexus.admin.reports.index', compact('reports', 'breadcrumbs'));
    }

    /**
     * Show the form for creating a report
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, $type, $id)
    {
        $validated = $request->validate([
            'reason' => 'required|string|max:255',
            'details' => 'nullable|string',
            'anonymous' => 'nullable|boolean',
        ]);

        // Resolve the reportable model
        $modelClass = match ($type) {
            'post' => \App\Models\Post::class,
            'chat' => \App\Models\Chat::class,
            default => abort(404, 'Invalid content type'),
        };

        $reportable = $modelClass::findOrFail($id);

        // Create and save the report
        $report = new Report;
        $report->reason = $validated['reason'];
        $report->details = $validated['details'] ?? null;
        $report->reported_content_snapshot = $reportable->toArray();

        if (! ($validated['anonymous'] ?? false) && Auth::check()) {
            $report->reporter_id = Auth::id();
        }

        $reportable->reports()->save($report);

        if ($report->reporter_id) {
            FlashHelper::showAlert('**Reported!** An administrator will be in touch as soon as possible', 'success');
        } else {
            FlashHelper::showAlert('**Reported!** An administrator will review your report as soon as possible', 'success');
        }

        $action = match ($type) {
            'post' => action('App\Http\Controllers\Nexus\TopicController@show', ['topic' => $reportable->topic->id]),
            'chat' => action('App\Http\Controllers\Nexus\ChatController@index'),
            default => action('App\Http\Controllers\Nexus\SectionController@index'),
        };

        return redirect($action);
    }

    /**
     * Display the specified resource.
     */
    public function show(Report $report)
    {
        $report->load('reportable', 'reporter');

        $breadcrumbs = BreadcrumbHelper::breadcumbForUtility('View Report');

        $postPreview = null;
        if ($report->reportable_type === \App\Models\Post::class) {
            $postPreview = $report->reportable;
        }

        $statusOptions = Report::STATUSES;

        return view('nexus.admin.reports.show', [
            'breadcrumbs' => $breadcrumbs,
            'report' => $report,
            'postPreview' => $postPreview,
            'statusOptions' => $statusOptions,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Report $report)
    {
        $request->validate([
            'status' => ['required', Rule::in(array_keys(Report::STATUSES))],
            'note' => ['nullable', 'string', 'max:5000'],
        ]);

        // Update the report's status
        $report->status = $request->input('status');
        $report->save();

        // If a note is provided, save it
        if ($request->filled('moderator_note')) {
            $report->moderationNotes()->create([
                'user_id' => Auth::id(),
                'user_name' => Auth::user()?->username ?? 'System',
                'note' => $request->input('moderator_note'),
            ]);
        }

        return redirect()
            ->route('reports.show', $report)
            ->with('success', 'Report updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}

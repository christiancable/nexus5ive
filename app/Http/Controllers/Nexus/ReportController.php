<?php

namespace App\Http\Controllers\Nexus;

use App\Helpers\BreadcrumbHelper;
use App\Helpers\FlashHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\Nexus\StoreReport;
use App\Http\Requests\Nexus\UpdateReport;
use App\Models\Chat;
use App\Models\Post;
use App\Models\Report;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ReportController extends Controller
{
    /**
     * index of reports, optionally filterable by status
     */
    public function index(Request $request): View
    {
        $breadcrumbs = BreadcrumbHelper::breadcumbForUtility('Moderation');

        $query = Report::query();

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        $totals = [
            'all' => Report::count(),
        ];

        foreach (Report::STATUSES as $key => $label) {
            $totals[$key] = Report::where('status', $key)->count();
        }

        $reports = $query->orderBy('created_at', 'desc')->paginate(15);

        return view('nexus.admin.reports.index', compact('reports', 'breadcrumbs', 'totals'));
    }

    public function create(): void
    {
        //
    }

    public function store(StoreReport $request, string $type, int $id): RedirectResponse
    {
        $validated = $request->validated();

        $modelClass = match ($type) {
            'post' => Post::class,
            'chat' => Chat::class,
            default => abort(404, 'Invalid content type'),
        };

        $reportable = $modelClass::findOrFail($id);

        $report = new Report;
        $report->status = 'new';
        $report->reason = $validated['reason'];
        $report->details = $validated['details'] ?? null;
        $report->reported_content_snapshot = $reportable->toArray();

        if (! ($validated['anonymous'] ?? false) && $request->user() !== null) {
            $report->reporter_id = $request->user()->id;
        }

        $reportable->reports()->save($report);

        if ($report->reporter_id) {
            FlashHelper::showAlert('**Reported!** An administrator will be in touch as soon as possible', 'success');
        } else {
            FlashHelper::showAlert('**Reported!** An administrator will review your report as soon as possible', 'success');
        }

        $action = match ($type) {
            'post' => route('topic.show', ['topic' => $reportable->topic->id]),
            'chat' => route('chat.index'),
        };

        return redirect($action);
    }

    public function show(Report $report): View
    {
        $report->load('reportable', 'reporter');

        $breadcrumbs = BreadcrumbHelper::breadcumbForUtility('View Report');

        $postPreview = null;
        if ($report->reportable_type === Post::class) {
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

    public function edit(string $id): void
    {
        //
    }

    public function update(UpdateReport $request, Report $report): RedirectResponse
    {
        $report->status = $request->input('status');
        $report->save();

        if ($request->filled('moderator_note')) {
            $report->moderationNotes()->create([
                'user_id' => $request->user()->id,
                'user_name' => $request->user()->username ?? 'System',
                'note' => $request->input('moderator_note'),
            ]);
        }

        $flashMessage = [
            'body' => 'Report updated successfully.',
            'level' => 'success',
        ];

        return redirect()
            ->route('reports.index')
            ->with('headerAlert', $flashMessage);
    }

    public function destroy(string $id): void
    {
        //
    }
}

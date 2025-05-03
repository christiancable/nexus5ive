<?php

namespace App\Http\Controllers\Nexus;

use App\Http\Controllers\Controller;
use App\Models\Post;
use App\Models\Report;
use App\Helpers\FlashHelper;

use Illuminate\Http\Request;

class ReportController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
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
        $report = new Report();
        $report->reason = $validated['reason'];
        $report->details = $validated['details'] ?? null;
        $report->reported_content_snapshot = $reportable->toJson();

        if (!($validated['anonymous'] ?? false) && auth()->check()) {
            $report->reporter_id = auth()->id();
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
    public function show(string $id)
    {
        //
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
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}

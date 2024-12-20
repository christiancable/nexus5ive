<?php

namespace App\Http\Controllers\Nexus;

use App\Helpers\ActivityHelper;
use App\Helpers\BreadcrumbHelper;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ActivityController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        ActivityHelper::updateActivity(
            $request->user()->id,
            'Checking out <em>who else is online</em>',
            action('App\Http\Controllers\Nexus\ActivityController@index')
        );
        $activities = ActivityHelper::recentActivities();
        $breadcrumbs = BreadcrumbHelper::breadcumbForUtility('Who is Online');

        $activityWindow = Carbon::now()->subMinutes(config('nexus.recent_activity'));

        return view('nexus.activities.index', compact('activities', 'breadcrumbs', 'activityWindow'));
    }
}

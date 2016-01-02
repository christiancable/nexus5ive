<?php
namespace Nexus\Helpers;

use Carbon\Carbon;

/* 
   helper methods for dealing with activities 
*/

class ActivityHelper
{

    /**
     * returns a collection of activities within the last NEXUS_RECENT_ACTIVITY minutes
     * @return EloquentCollection of recent activities
     */
    public static function recentActivities()
    {

        $within = env('NEXUS_RECENT_ACTIVITY');
        $activities =  \Nexus\Activity::where('time', '>=', Carbon::now()->subMinutes($within))
            ->get();
        return $activities;
    }

    /**
     * updates or creates the current record of a user's activity in the BBS
     * @param string $text description of the activity eg 'Reading AreaOrange'
     * @param string $route the url of the activity if it exists eg '/topics/123'
     */
    public static function updateActivity($text = null, $route = null)
    {
        $activity = \Nexus\Activity::firstOrNew(['user_id' => \Auth::user()->id]);
        $activity->text = $text;
        $activity->route = $route;
        $activity->time = time();
        $activity->save();
    }
}
